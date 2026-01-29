<?php

namespace App\Services\Pages;

use Illuminate\Http\UploadedFile;

class PageImageService
{
    /** Pages only ever delete files they uploaded themselves. */
    private const OWN_DIRECTORY = 'pages/';

    public function upload(?UploadedFile $file, ?string $oldPath = null, string $directory = 'pages/about'): ?string
    {
        if (! $file) {
            return $oldPath;
        }

        $newPath = $file->store($directory, config('filesystems.media_disk'));

        $this->deleteIfExists($oldPath);

        return $newPath;
    }

    public function deleteIfExists(?string $path): void
    {
        if ($this->isOwnFile($path) && media_disk()->exists($path)) {
            media_disk()->delete($path);
        }
    }

    public function publicUrl(?string $path): string
    {
        if (! $path) {
            return asset('images/about-01.jpg');
        }

        if ($this->isExternalUrl($path)) {
            return $path;
        }

        if ($this->isAssetPath($path)) {
            return asset($path);
        }

        return media_url($path);
    }

    private function isExternalUrl(string $path): bool
    {
        return str_starts_with($path, 'http://') || str_starts_with($path, 'https://');
    }

    private function isAssetPath(string $path): bool
    {
        return str_starts_with($path, 'images/');
    }

    /**
     * A file this service stored: under pages/, relative, with no "..".
     */
    public function isOwnFile(?string $path): bool
    {
        if (! is_string($path) || $path === '' || $this->isExternalUrl($path) || $this->isAssetPath($path)) {
            return false;
        }

        $normalized = str_replace('\\', '/', $path);

        return str_starts_with($normalized, self::OWN_DIRECTORY)
            && ! str_contains($normalized, '..')
            && ! str_contains($normalized, "\0");
    }
}
