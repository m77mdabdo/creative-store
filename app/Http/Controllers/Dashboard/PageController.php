<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Page\DeletePageRequest;
use App\Http\Requests\Dashboard\Page\StorePageRequest;
use App\Http\Requests\Dashboard\Page\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:200'],
            'status' => ['nullable', 'in:0,1'],
            'sort' => ['nullable', 'in:newest,oldest,slug_asc,slug_desc'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
        ]);

        $perPage = (int) ($filters['per_page'] ?? 15);
        $sort = $filters['sort'] ?? 'newest';

        $query = Page::query()
            ->with(['translations'])
            ->search($filters['q'] ?? null);

        if (($filters['status'] ?? '') !== '') {
            $query->where('is_active', (bool) $filters['status']);
        }

        $query = match ($sort) {
            'oldest' => $query->oldest('id'),
            'slug_asc' => $query->orderBy('slug'),
            'slug_desc' => $query->orderByDesc('slug'),
            default => $query->latest('id'),
        };

        $pages = $query->paginate($perPage)->withQueryString();

        return view('dashboard.pages.index', [
            'pages' => $pages,
            'filters' => [
                'q' => $filters['q'] ?? '',
                'status' => $filters['status'] ?? '',
                'sort' => $sort,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function create(): View
    {
        return view('dashboard.pages.create');
    }

    public function store(StorePageRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $page = Page::create([
            'slug' => $data['slug'],
            'is_active' => $data['is_active'] ?? false,
        ]);

        $page->translations()->createMany([
            [
                'locale' => 'ar',
                'title' => $data['title_ar'],
                'content' => $data['content_ar'],
            ],
            [
                'locale' => 'en',
                'title' => $data['title_en'],
                'content' => $data['content_en'],
            ],
        ]);

        return redirect()
            ->route('dashboard.pages.index')
            ->with('success', "Page [{$page->slug}] created successfully.");
    }

    public function show(Page $page): View
    {
        $page->load(['translations']);

        $ar = $page->translations->firstWhere('locale', 'ar');
        $en = $page->translations->firstWhere('locale', 'en');

        return view('dashboard.pages.show', [
            'page' => $page,
            'translationAr' => $ar,
            'translationEn' => $en,
        ]);
    }

    public function edit(Page $page): View
    {
        $page->load(['translations']);

        $ar = $page->translations->firstWhere('locale', 'ar');
        $en = $page->translations->firstWhere('locale', 'en');

        return view('dashboard.pages.edit', [
            'page' => $page,
            'translationAr' => $ar,
            'translationEn' => $en,
        ]);
    }

    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $data = $request->validated();

        $page->update([
            'slug' => $data['slug'],
            'is_active' => $data['is_active'] ?? false,
        ]);

        $page->translations()->updateOrCreate(
            ['locale' => 'ar'],
            [
                'title' => $data['title_ar'],
                'content' => $data['content_ar'],
            ]
        );

        $page->translations()->updateOrCreate(
            ['locale' => 'en'],
            [
                'title' => $data['title_en'],
                'content' => $data['content_en'],
            ]
        );

        return redirect()
            ->route('dashboard.pages.index')
            ->with('success', "Page [{$page->slug}] updated successfully.");
    }

    public function destroy(DeletePageRequest $request, Page $page): RedirectResponse
    {
        $slug = $page->slug;
        $page->delete();

        return redirect()
            ->route('dashboard.pages.index')
            ->with('success', "Page [{$slug}] deleted successfully.");
    }

    public function toggleStatus(Page $page): RedirectResponse
    {
        $page->update([
            'is_active' => ! $page->is_active,
        ]);

        return redirect()
            ->route('dashboard.pages.index')
            ->with('success', 'Page status updated successfully.');
    }
}
