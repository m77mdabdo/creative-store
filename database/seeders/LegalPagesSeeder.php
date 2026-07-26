<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Store policy pages required before taking real payments: privacy, terms,
 * returns, shipping and FAQ, in Arabic and English.
 *
 * Only missing pages are created, so edits made later in the dashboard are
 * never overwritten. Replace the [bracketed] placeholders with the store's
 * registration details and have the texts reviewed before launch.
 *
 *   php artisan db:seed --class=LegalPagesSeeder
 */
class LegalPagesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (LegalPagesContent::pages() as $slug => $locales) {
            if (Page::query()->where('slug', $slug)->exists()) {
                $this->command?->line("Skipped {$slug} (already exists).");
                continue;
            }

            DB::transaction(function () use ($slug, $locales) {
                $page = Page::query()->create(['slug' => $slug, 'is_active' => true]);

                foreach ($locales as $locale => [$title, $content]) {
                    $page->translations()->create([
                        'locale'  => $locale,
                        'title'   => $title,
                        'content' => trim($content),
                    ]);
                }
            });

            $this->command?->info("Created {$slug}.");
        }
    }
}
