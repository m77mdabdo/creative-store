<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendLibrariesTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_storefront_loads_patched_libraries_once(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'jquery-3.7.1.min.js'));
        $this->assertStringContainsString('bootstrap/js/bootstrap.bundle.min.js', $html);
        $this->assertStringNotContainsString('jquery-3.2.1', $html);
        $this->assertStringNotContainsString('daterangepicker', $html);
    }

    public function test_the_library_files_exist(): void
    {
        $this->assertFileExists(public_path('cozastore/vendor/jquery/jquery-3.7.1.min.js'));
        $this->assertStringContainsString('jQuery v3.7.1', file_get_contents(public_path('cozastore/vendor/jquery/jquery-3.7.1.min.js')));
        $this->assertStringContainsString('Bootstrap v4.6.2', file_get_contents(public_path('cozastore/vendor/bootstrap/js/bootstrap.bundle.min.js')));
        $this->assertStringContainsString('Select2 4.0.13', file_get_contents(public_path('cozastore/vendor/select2/select2.min.js')));
    }

    public function test_live_updates_start_once_and_only_with_a_reverb_key(): void
    {
        $echo = file_get_contents(resource_path('js/echo.js'));
        $app = file_get_contents(resource_path('js/app.js'));

        $this->assertSame(1, substr_count($echo . $app, 'new Echo('));
        $this->assertStringContainsString('if (reverbKey)', $echo);
        $this->assertStringNotContainsString('require(', $app);

        foreach (['.env.example', '.env.production.example'] as $file) {
            $this->assertStringContainsString('VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"', file_get_contents(base_path($file)));
        }
    }
}
