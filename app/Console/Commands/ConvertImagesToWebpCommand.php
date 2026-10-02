<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Create a WebP copy next to every uploaded JPEG/PNG; media_url() then
 * serves the WebP automatically. Originals are kept. Safe to re-run: files
 * that already have a WebP copy are skipped.
 */
class ConvertImagesToWebpCommand extends Command
{
    protected $signature = 'media:webp
        {--quality=80 : WebP quality (1-100)}
        {--max-width=1600 : Downscale wider images to this width}
        {--path= : Only convert files under this folder}';

    protected $description = 'Create smaller WebP copies of uploaded images';

    public function handle(): int
    {
        if (! function_exists('imagewebp')) {
            $this->error('The GD extension with WebP support is required (php -m | grep gd).');

            return self::FAILURE;
        }

        $disk = Storage::disk(config('filesystems.media_disk', 'public'));
        $quality = max(1, min(100, (int) $this->option('quality')));
        $maxWidth = max(200, (int) $this->option('max-width'));
        $converted = $skipped = $saved = 0;

        foreach ($disk->allFiles((string) $this->option('path')) as $file) {
            if (! preg_match('/\.(jpe?g|png)$/i', $file)) {
                continue;
            }

            $target = preg_replace('/\.(jpe?g|png)$/i', '.webp', $file);

            if ($disk->exists($target)) {
                $skipped++;
                continue;
            }

            $image = @imagecreatefromstring($disk->get($file));

            if (! $image) {
                $this->warn("Skipped unreadable image: {$file}");
                continue;
            }

            if (imagesx($image) > $maxWidth) {
                $image = imagescale($image, $maxWidth);
            }

            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);

            ob_start();
            imagewebp($image, null, $quality);
            $webp = ob_get_clean();
            imagedestroy($image);

            $disk->put($target, $webp);
            $saved += max(0, $disk->size($file) - strlen($webp));
            $converted++;
        }

        $this->info(sprintf('%d converted, %d already done, %.1f MB saved.', $converted, $skipped, $saved / 1048576));

        return self::SUCCESS;
    }
}
