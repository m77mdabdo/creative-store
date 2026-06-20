<?php

namespace App\Services\Pages;

use Illuminate\Http\UploadedFile;

class PageImageService
{
    public function upload(?UploadedFile $file, ?string $oldPath = null, string $directory = 'pages/about'): ?string
    {
        if (! $file) {
            return $oldPath;
        }

        $newPath = $file->store($directory, config('filesystems.media_disk'));

        if (
            $oldPath &&
            ! $this->isExternalUrl($oldPath) &&
            ! $this->isAssetPath($oldPath) &&
            media_disk()->exists($oldPath)
        ) {
            media_disk()->delete($oldPath);
        }

        return $newPath;
    }

    public function deleteIfExists(?string $path): void
    {
        if (
            $path &&
            ! $this->isExternalUrl($path) &&
            ! $this->isAssetPath($path) &&
            media_disk()->exists($path)
        ) {
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
}
