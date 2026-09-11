<?php

namespace Tests\Feature\Storefront;

use App\Models\Page;
use Database\Seeders\LegalPagesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_policy_pages_are_seeded_with_the_store_details(): void
    {
        config(['store.legal.vat_number' => '311111111111113', 'store.support_email' => 'care@example.sa']);

        $this->seed(LegalPagesSeeder::class);

        $terms = Page::query()->where('slug', 'terms')->firstOrFail();
        $content = $terms->translate('en')->content;

        $this->assertStringContainsString('311111111111113', $content);
        $this->assertStringContainsString('care@example.sa', $content);
        $this->assertDoesNotMatchRegularExpression('/\[[^\]]+\]/', $content);

        $this->get(route('frontend.pages.show', 'terms'))->assertOk()->assertSee('Terms and Conditions');
    }

    public function test_old_placeholder_pages_are_refreshed_but_edited_pages_are_kept(): void
    {
        $this->seed(LegalPagesSeeder::class);

        $privacy = Page::query()->where('slug', 'privacy')->firstOrFail();
        $privacy->translations()->where('locale', 'en')->update(['content' => 'Contact [support email]']);
        $faq = Page::query()->where('slug', 'faq')->firstOrFail();
        $faq->translations()->where('locale', 'en')->update(['content' => 'Edited by the owner']);

        $this->seed(LegalPagesSeeder::class);

        $this->assertStringNotContainsString('[support email]', $privacy->fresh()->translate('en')->content);
        $this->assertSame('Edited by the owner', $faq->fresh()->translate('en')->content);
    }
}
