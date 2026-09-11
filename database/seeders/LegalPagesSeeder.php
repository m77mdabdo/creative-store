<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Store policy pages required before taking real payments: privacy, terms,
 * returns, shipping and FAQ, in Arabic and English.
 *
 * Missing pages are created; pages that still hold the old [bracketed]
 * placeholders are refreshed with the details from config/store.php.
 * Pages edited in the dashboard are never overwritten.
 *
 *   php artisan db:seed --class=LegalPagesSeeder
 */
class LegalPagesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (LegalPagesContent::pages() as $slug => $locales) {
            $existing = Page::query()->where('slug', $slug)->first();

            if ($existing) {
                $this->refreshPlaceholders($existing, $locales);
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

    private function refreshPlaceholders(Page $page, array $locales): void
    {
        $refreshed = false;

        foreach ($locales as $locale => [$title, $content]) {
            $translation = $page->translations()->where('locale', $locale)->first();

            if ($translation && preg_match('/\[[^\]]{2,40}\]/u', (string) $translation->content)) {
                $translation->update(['content' => trim($content)]);
                $refreshed = true;
            }
        }

        $this->command?->line($refreshed ? "Filled placeholders in {$page->slug}." : "Skipped {$page->slug} (already set).");
    }
}
