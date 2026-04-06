<?php

namespace App\Services\Pages;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PageImageService
{
    public function upload(?UploadedFile $file, ?string $oldPath = null, string $directory = 'pages/about'): ?string
    {
        if (! $file) {
            return $oldPath;
        }

        $newPath = $file->store($directory);

        if (
            $oldPath &&
            ! $this->isExternalUrl($oldPath) &&
            ! $this->isAssetPath($oldPath) &&
            Storage::exists($oldPath)
        ) {
            Storage::delete($oldPath);
        }

        return $newPath;
    }

    public function deleteIfExists(?string $path): void
    {
        if (
            $path &&
            ! $this->isExternalUrl($path) &&
            ! $this->isAssetPath($path) &&
            Storage::exists($path)
        ) {
            Storage::delete($path);
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

        return Storage::url($path);
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
