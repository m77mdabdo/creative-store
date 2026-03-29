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

        $newPath = $file->store($directory, 'public');

        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        return $newPath;
    }

    public function deleteIfExists(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function publicUrl(?string $path): string
    {
        if (! $path) {
            return asset('images/about-01.jpg');
        }

        if (
            str_starts_with($path, 'http://') ||
            str_starts_with($path, 'https://')
        ) {
            return $path;
        }

        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}
