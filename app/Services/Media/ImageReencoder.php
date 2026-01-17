<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Re-draws an uploaded image onto a clean canvas and saves it as WebP.
 *
 * This drops everything that is not pixels: EXIF and GPS location from
 * phone photos, camera serial numbers, comments, and anything appended
 * after the image data (the usual trick for hiding script in a "JPEG").
 */
class ImageReencoder
{
    private const QUALITY = 85;

    /** Longest side kept, so a 50-megapixel photo is not stored as an avatar. */
    private const MAX_SIDE = 1024;

    public function store(UploadedFile $file, string $directory): string
    {
        $source = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if ($source === false) {
            throw new RuntimeException('The uploaded file is not a readable image.');
        }

        $canvas = $this->resized($source);
        imagedestroy($source);

        ob_start();
        imagewebp($canvas, null, self::QUALITY);
        $bytes = (string) ob_get_clean();
        imagedestroy($canvas);

        $path = trim($directory, '/') . '/' . Str::random(40) . '.webp';
        media_disk()->put($path, $bytes);

        return $path;
    }

    private function resized(\GdImage $source): \GdImage
    {
        $width  = imagesx($source);
        $height = imagesy($source);
        $scale  = min(1, self::MAX_SIDE / max($width, $height));
        $newW   = max(1, (int) round($width * $scale));
        $newH   = max(1, (int) round($height * $scale));

        $canvas = imagecreatetruecolor($newW, $newH);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newW, $newH, $width, $height);

        return $canvas;
    }
}
