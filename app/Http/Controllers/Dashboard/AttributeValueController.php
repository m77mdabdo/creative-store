<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Attribute\StoreAttributeValueRequest;
use App\Http\Requests\Dashboard\Attribute\UpdateAttributeValueRequest;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AttributeValueController extends Controller
{
    public function create(Attribute $attribute): View
    {
        return view('dashboard.attribute_values.create', [
            'attribute' => $attribute,
            'form' => $this->formData(),
        ]);
    }

    public function store(StoreAttributeValueRequest $request, Attribute $attribute): RedirectResponse
    {
        $payload = $this->normalizedPayload($request, $attribute);

        $attribute->values()->create($payload);

        return redirect()
            ->route('dashboard.attributes.show', $attribute)
            ->with('success', 'Attribute value created successfully.');
    }

    public function edit(Attribute $attribute, AttributeValue $value): View
    {
        abort_unless($value->attribute_id === $attribute->id, 404);

        return view('dashboard.attribute_values.edit', [
            'attribute' => $attribute,
            'value' => $value,
            'form' => $this->formData($value),
        ]);
    }

    public function update(UpdateAttributeValueRequest $request, Attribute $attribute, AttributeValue $value): RedirectResponse
    {
        abort_unless($value->attribute_id === $attribute->id, 404);

        $payload = $this->normalizedPayload($request, $attribute, $value);

        $value->update($payload);

        return redirect()
            ->route('dashboard.attributes.show', $attribute)
            ->with('success', 'Attribute value updated successfully.');
    }

    public function toggleStatus(Attribute $attribute, AttributeValue $value): RedirectResponse
    {
        abort_unless($value->attribute_id === $attribute->id, 404);

        $value->update([
            'is_active' => ! $value->is_active,
        ]);

        return back()->with('success', 'Attribute value status updated successfully.');
    }

    public function destroy(Attribute $attribute, AttributeValue $value): RedirectResponse
    {
        abort_unless($value->attribute_id === $attribute->id, 404);

        if ($value->variantValues()->exists()) {
            return back()->with('error', 'Cannot delete this value because it is linked to product variants.');
        }

        $value->delete();

        return back()->with('success', 'Attribute value deleted successfully.');
    }

    private function formData(?AttributeValue $value = null): array
    {
        return [
            'value' => old('value', $value->value ?? ''),
            'slug' => old('slug', $value->slug ?? ''),
            'sort_order' => old('sort_order', $value->sort_order ?? 0),
            'is_active' => old('is_active', $value?->is_active ?? true),
        ];
    }

    private function normalizedPayload(Request $request, Attribute $attribute, ?AttributeValue $value = null): array
    {
        $rawValue = trim((string) $request->input('value'));
        $slug = trim((string) $request->input('slug', ''));

        if ($slug === '') {
            $slug = $rawValue;
        }

        $slug = $this->generateUniqueSlug($attribute, $slug, $value?->id);

        return [
            'value' => $rawValue,
            'slug' => $slug,
            'sort_order' => (int) $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active', true),
        ];
    }

    private function generateUniqueSlug(Attribute $attribute, string $slug, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug(Str::lower(trim($slug)));

        if ($baseSlug === '') {
            $baseSlug = 'value';
        }

        $uniqueSlug = $baseSlug;
        $counter = 1;

        while (
            AttributeValue::query()
                ->where('attribute_id', $attribute->id)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $uniqueSlug)
                ->exists()
        ) {
            $uniqueSlug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $uniqueSlug;
    }
}
