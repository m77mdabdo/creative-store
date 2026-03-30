<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Category extends Model
{
    use HasFactory, SoftDeletes , Auditable ;

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
        return $this->children()->with('childrenRecursive', 'translations');
    }

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
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

    /* =========================
     * Translation helpers
     * ========================= */
    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', config('app.fallback_locale'))
            ?? $this->translations->first();
    }

    public function getNameAttribute()
    {
        return optional($this->translation())->name;
    }

    public function getDescriptionAttribute()
    {
        return optional($this->translation())->description;
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
