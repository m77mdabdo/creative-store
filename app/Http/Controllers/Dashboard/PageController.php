<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Page\StorePageRequest;
use App\Http\Requests\Dashboard\Page\UpdatePageRequest;
use App\Models\Page;
use App\Services\Pages\PageImageService;
use App\Support\PageContent\AboutPageContent;
use App\Support\PageContent\ContactPageContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(
        protected PageImageService $pageImageService
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'q'        => trim((string) $request->input('q', '')),
            'status'   => (string) $request->input('status', ''),
            'sort'     => (string) $request->input('sort', 'newest'),
            'per_page' => (int) $request->input('per_page', 15),
        ];

        if (! in_array($filters['sort'], ['newest', 'oldest', 'slug_asc', 'slug_desc'], true)) {
            $filters['sort'] = 'newest';
        }

        if (! in_array($filters['per_page'], [10, 15, 25, 50, 100], true)) {
            $filters['per_page'] = 15;
        }

        $query = Page::query()
            ->with(['translations:id,page_id,locale,title'])
            ->search($filters['q']);

        if ($filters['status'] !== '') {
            $query->where('is_active', $filters['status'] === '1');
        }

        switch ($filters['sort']) {
            case 'oldest':
                $query->oldest('id');
                break;
            case 'slug_asc':
                $query->orderBy('slug', 'asc');
                break;
            case 'slug_desc':
                $query->orderBy('slug', 'desc');
                break;
            default:
                $query->latest('id');
                break;
        }

        $pages = $query->paginate($filters['per_page'])->withQueryString();

        return view('dashboard.pages.index', compact('pages', 'filters'));
    }

    public function show(Page $page): View
    {
        $page->load(['translations']);

        $translationAr = $page->translations->firstWhere('locale', 'ar');
        $translationEn = $page->translations->firstWhere('locale', 'en');

        $aboutAr = $page->slug === 'about'
            ? AboutPageContent::decode($translationAr?->content)
            : [];

        $aboutEn = $page->slug === 'about'
            ? AboutPageContent::decode($translationEn?->content)
            : [];

        $contactAr = $page->slug === 'contact'
            ? ContactPageContent::decode($translationAr?->content)
            : [];

        $contactEn = $page->slug === 'contact'
            ? ContactPageContent::decode($translationEn?->content)
            : [];

        return view('dashboard.pages.show', compact(
            'page',
            'translationAr',
            'translationEn',
            'aboutAr',
            'aboutEn',
            'contactAr',
            'contactEn'
        ));
    }

    public function create(): View
    {
        return view('dashboard.pages.create');
    }

    public function store(StorePageRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data) {
            $page = Page::query()->create([
                'slug' => $data['slug'],
                'is_active' => (bool) ($data['is_active'] ?? true),
            ]);

            [$contentAr, $contentEn] = $this->buildTranslationContents($request, $data);

            $page->translations()->createMany([
                [
                    'locale' => 'ar',
                    'title' => $data['title_ar'],
                    'content' => $contentAr,
                ],
                [
                    'locale' => 'en',
                    'title' => $data['title_en'],
                    'content' => $contentEn,
                ],
            ]);

            Page::clearCacheBySlug($page->slug);
        });

        return redirect()
            ->route('dashboard.pages.index')
            ->with('success', __('Page created successfully.'));
    }

    public function edit(Page $page): View
    {
        $page->load(['translations']);

        $translationAr = $page->translations->firstWhere('locale', 'ar');
        $translationEn = $page->translations->firstWhere('locale', 'en');

        $aboutAr = $page->slug === 'about'
            ? AboutPageContent::decode($translationAr?->content)
            : [];

        $aboutEn = $page->slug === 'about'
            ? AboutPageContent::decode($translationEn?->content)
            : [];

        $contactAr = $page->slug === 'contact'
            ? ContactPageContent::decode($translationAr?->content)
            : [];

        $contactEn = $page->slug === 'contact'
            ? ContactPageContent::decode($translationEn?->content)
            : [];

        return view('dashboard.pages.edit', compact(
            'page',
            'translationAr',
            'translationEn',
            'aboutAr',
            'aboutEn',
            'contactAr',
            'contactEn'
        ));
    }

    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $data = $request->validated();
        $oldSlug = $page->slug;

        DB::transaction(function () use ($request, $page, $data, $oldSlug) {
            $page->update([
                'slug' => $data['slug'],
                'is_active' => (bool) ($data['is_active'] ?? false),
            ]);

            [$contentAr, $contentEn] = $this->buildTranslationContents($request, $data, $page);

            $page->translations()->updateOrCreate(
                ['locale' => 'ar'],
                [
                    'title' => $data['title_ar'],
                    'content' => $contentAr,
                ]
            );

            $page->translations()->updateOrCreate(
                ['locale' => 'en'],
                [
                    'title' => $data['title_en'],
                    'content' => $contentEn,
                ]
            );

            Page::clearCacheBySlug($oldSlug);
            Page::clearCacheBySlug($page->slug);
        });

        return redirect()
            ->route('dashboard.pages.index')
            ->with('success', __('Page updated successfully.'));
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->load(['translations']);

        if ($page->slug === 'about') {
            foreach ($page->translations as $translation) {
                $content = AboutPageContent::decode($translation->content);
                $this->pageImageService->deleteIfExists($content['image_1'] ?? null);
                $this->pageImageService->deleteIfExists($content['image_2'] ?? null);
            }
        }

        $slug = $page->slug;
        $page->delete();

        Page::clearCacheBySlug($slug);

        return redirect()
            ->route('dashboard.pages.index')
            ->with('success', __('Page deleted successfully.'));
    }

    public function toggleStatus(Page $page): RedirectResponse
    {
        $page->update([
            'is_active' => ! $page->is_active,
        ]);

        Page::clearCacheBySlug($page->slug);

        return redirect()
            ->route('dashboard.pages.index')
            ->with('success', __('Page status updated successfully.'));
    }

    private function buildTranslationContents(StorePageRequest|UpdatePageRequest $request, array $data, ?Page $page = null): array
    {
        $slug = $data['slug'] ?? '';

        if ($slug === 'about') {
            return $this->buildAboutContents($request, $data, $page);
        }

        if ($slug === 'contact') {
            return [
                ContactPageContent::encode($data, 'ar'),
                ContactPageContent::encode($data, 'en'),
            ];
        }

        return [
            $data['content_ar'] ?? '',
            $data['content_en'] ?? '',
        ];
    }

    private function buildAboutContents(StorePageRequest|UpdatePageRequest $request, array $data, ?Page $page = null): array
    {
        $existingAr = [];
        $existingEn = [];

        if ($page) {
            $page->loadMissing('translations');

            $existingAr = AboutPageContent::decode(
                $page->translations->firstWhere('locale', 'ar')?->content
            );

            $existingEn = AboutPageContent::decode(
                $page->translations->firstWhere('locale', 'en')?->content
            );
        }

        $data['image_1_ar'] = $this->pageImageService->upload(
            $request->file('image_1_ar_file'),
            $existingAr['image_1'] ?? ($data['image_1_ar'] ?? 'images/about-01.jpg')
        );

        $data['image_2_ar'] = $this->pageImageService->upload(
            $request->file('image_2_ar_file'),
            $existingAr['image_2'] ?? ($data['image_2_ar'] ?? 'images/about-02.jpg')
        );

        $data['image_1_en'] = $this->pageImageService->upload(
            $request->file('image_1_en_file'),
            $existingEn['image_1'] ?? ($data['image_1_en'] ?? 'images/about-01.jpg')
        );

        $data['image_2_en'] = $this->pageImageService->upload(
            $request->file('image_2_en_file'),
            $existingEn['image_2'] ?? ($data['image_2_en'] ?? 'images/about-02.jpg')
        );

        return [
            AboutPageContent::encode($data, 'ar'),
            AboutPageContent::encode($data, 'en'),
        ];
    }
}
