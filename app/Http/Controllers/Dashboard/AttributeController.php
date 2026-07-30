<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Attribute\StoreAttributeRequest;
use App\Http\Requests\Dashboard\Attribute\UpdateAttributeRequest;
use App\Models\Attribute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AttributeController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search' => trim((string) $request->input('search', '')),
            'status' => (string) $request->input('status', ''),
        ];

        $query = Attribute::query()
            ->withCount(['values', 'products'])
            ->with(['values' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')]);

        if ($filters['search'] !== '') {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($filters['status'] === 'active') {
            $query->where('is_active', true);
        } elseif ($filters['status'] === 'inactive') {
            $query->where('is_active', false);
        }

        $attributes = $query
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.attributes.index', [
            'attributes' => $attributes,
            'filters' => $filters,
            'canCreate' => $this->canCurrentUser('attributes.create'),
            'canEdit' => $this->canCurrentUser('attributes.edit'),
            'canDelete' => $this->canCurrentUser('attributes.delete'),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.attributes.create', [
            'form' => $this->formData(),
        ]);
    }

    public function store(StoreAttributeRequest $request): RedirectResponse
    {
        $payload = $this->normalizedPayload($request);

        Attribute::create($payload);

        return redirect()
            ->route('dashboard.attributes.index')
            ->with('success', __('Attribute created successfully.'));
    }

    public function show(Attribute $attribute): View
    {
        $attribute->load([
            'values' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
            'products.translations',
        ])->loadCount(['values', 'products']);

        return view('dashboard.attributes.show', [
            'attribute' => $attribute,
            'canEdit' => $this->canCurrentUser('attributes.edit'),
            'canDelete' => $this->canCurrentUser('attributes.delete'),
        ]);
    }

    public function edit(Attribute $attribute): View
    {
        return view('dashboard.attributes.edit', [
            'attribute' => $attribute,
            'form' => $this->formData($attribute),
        ]);
    }

    public function update(UpdateAttributeRequest $request, Attribute $attribute): RedirectResponse
    {
        $payload = $this->normalizedPayload($request, $attribute);

        $attribute->update($payload);

        return redirect()
            ->route('dashboard.attributes.index')
            ->with('success', __('Attribute updated successfully.'));
    }

    public function toggleStatus(Attribute $attribute): RedirectResponse
    {
        $attribute->update([
            'is_active' => ! $attribute->is_active,
        ]);

        return back()->with('success', __('Attribute status updated successfully.'));
    }

    public function destroy(Attribute $attribute): RedirectResponse
    {
        if ($attribute->values()->exists() || $attribute->products()->exists()) {
            return back()->with('error', __('Cannot delete this attribute because it is linked to values or products.'));
        }

        $attribute->delete();

        return redirect()
            ->route('dashboard.attributes.index')
            ->with('success', __('Attribute deleted successfully.'));
    }

    private function formData(?Attribute $attribute = null): array
    {
        return [
            'name' => old('name', $attribute->name ?? ''),
            'slug' => old('slug', $attribute->slug ?? ''),
            'is_active' => old('is_active', $attribute?->is_active ?? true),
        ];
    }

    private function normalizedPayload(Request $request, ?Attribute $attribute = null): array
    {
        $name = trim((string) $request->input('name'));
        $slug = trim((string) $request->input('slug', ''));

        if ($slug === '') {
            $slug = $name;
        }

        $slug = $this->generateUniqueSlug($slug, $attribute?->id);

        return [
            'name' => $name,
            'slug' => $slug,
            'is_active' => $request->boolean('is_active', true),
        ];
    }

    private function generateUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug(Str::lower(trim($slug)));

        if ($baseSlug === '') {
            $baseSlug = 'attribute';
        }

        $uniqueSlug = $baseSlug;
        $counter = 1;

        while (
            Attribute::query()
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $uniqueSlug)
                ->exists()
        ) {
            $uniqueSlug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $uniqueSlug;
    }

    private function canCurrentUser(string $permission): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasRole('owner')
            || $user->hasRole('admin')
            || $user->hasPermission($permission);
    }
}
