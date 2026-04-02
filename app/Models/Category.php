<?php

namespace App\Models;

use App\Traits\Auditable;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, SoftDeletes, Auditable, Translatable;

    public array $translatedAttributes = [
        'name',
        'description',
    ];

    protected $fillable = [
        'parent_id',
        'slug',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /* =========================
     * Relationships
     * ========================= */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public function childrenRecursive()
    {
        return $this->children()->with(['childrenRecursive', 'translations']);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /* =========================
     * Scopes
     * ========================= */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }

    public function scopeSearch($query, ?string $term)
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('slug', 'like', "%{$term}%")
                ->orWhereHas('translations', function ($t) use ($term) {
                    $t->where('name', 'like', "%{$term}%")
                      ->orWhere('description', 'like', "%{$term}%");
                });
        });
    }

    /* =========================
     * Helpers
     * ========================= */
    public function isParentOf(Category $category): bool
    {
        return $category->parent_id === $this->id;
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    public function getBreadcrumbAttribute(): string
    {
        $names = [];
        $current = $this;

        while ($current) {
            $names[] = $current->name ?: "Category #{$current->id}";
            $current = $current->parent;
        }

        return implode(' > ', array_reverse($names));
    }
}
