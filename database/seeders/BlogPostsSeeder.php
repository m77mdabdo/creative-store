<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Bilingual editorial posts (style guides, fragrance, accessories, care,
 * men's style, occasions) illustrated with catalogue photos.
 *
 * Run after DemoCatalogSeeder (it provides the photos and products):
 *   php artisan db:seed --class=BlogPostsSeeder
 *
 * Safe to re-run: posts and categories are matched by slug.
 */
class BlogPostsSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [];

        foreach (BlogPostsData::categories() as $slug => [$name, $nameAr]) {
            $categories[$slug] = BlogCategory::query()->updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'name_ar' => $nameAr],
            );
        }

        $liveSlugs = Product::query()->where('status', 'active')->pluck('slug')->flip();

        DB::transaction(function () use ($categories, $liveSlugs) {
            foreach (BlogPostsData::posts() as $index => $post) {
                $publishedAt = now()->subDays($post['days_ago'])->setTime(9 + ($index % 8), 15 * ($index % 4));

                Blog::query()->updateOrCreate(
                    ['slug' => $post['slug']],
                    [
                        'blog_category_id' => $categories[$post['category']]->id,
                        'title'            => $post['en']['title'],
                        'title_ar'         => $post['ar']['title'],
                        'slug_ar'          => $post['slug'],
                        'excerpt'          => $post['en']['excerpt'],
                        'excerpt_ar'       => $post['ar']['excerpt'],
                        'content'          => $this->links($post['en']['content'], 'en', $liveSlugs),
                        'content_ar'       => $this->links($post['ar']['content'], 'ar', $liveSlugs),
                        'image'            => "products/catalog/{$post['photo']}.jpg",
                        'author'           => 'Creative Store',
                        'is_featured'      => $post['featured'],
                        'status'           => true,
                        'views'            => 120 + (($index * 37) % 400),
                        'published_at'     => $publishedAt,
                    ],
                );
            }
        });

        $this->command?->info('Blog posts: ' . count(BlogPostsData::posts()) . ' in ' . count($categories) . ' categories.');
    }

    /**
     * {{slug|label}} → link to the product in that language, or plain text
     * when the product is not on sale, so posts never link to a 404.
     */
    private function links(string $html, string $locale, $liveSlugs): string
    {
        return preg_replace_callback('/\{\{([a-z0-9-]+)\|([^}]+)\}\}/u', function (array $m) use ($locale, $liveSlugs) {
            $label = e($m[2]);

            if (! $liveSlugs->has($m[1])) {
                return $label;
            }

            return '<a href="/' . $locale . '/products/' . $m[1] . '">' . $label . '</a>';
        }, trim($html));
    }
}
