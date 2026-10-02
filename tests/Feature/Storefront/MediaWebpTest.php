<?php

namespace Tests\Feature\Storefront;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaWebpTest extends TestCase
{
    public function test_images_get_a_webp_copy_that_media_url_prefers(): void
    {
        if (! function_exists('imagewebp')) {
            $this->markTestSkipped('GD with WebP support is not installed.');
        }

        Storage::fake('public');
        config(['filesystems.media_disk' => 'public']);

        $image = imagecreatetruecolor(2400, 1200);
        imagefill($image, 0, 0, imagecolorallocate($image, 200, 120, 80));
        ob_start();
        imagejpeg($image, null, 95);
        Storage::disk('public')->put('products/catalog/sample.jpg', ob_get_clean());

        $this->artisan('media:webp', ['--max-width' => 1600])->assertSuccessful();

        Storage::disk('public')->assertExists('products/catalog/sample.webp');
        [$width] = getimagesizefromstring(Storage::disk('public')->get('products/catalog/sample.webp'));
        $this->assertSame(1600, $width);
        $this->assertStringEndsWith('storage/products/catalog/sample.webp', media_url('products/catalog/sample.jpg'));

        // Running again does nothing new.
        $this->artisan('media:webp')->expectsOutputToContain('0 converted, 1 already done')->assertSuccessful();
    }
}
