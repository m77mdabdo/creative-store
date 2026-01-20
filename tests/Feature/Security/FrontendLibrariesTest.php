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
}
