<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\ProductImage;

class MoveImagesToS3 extends Command
{
    protected $signature = 'images:move-to-s3';
    protected $description = 'Move images from local (public) to S3 (Laravel Cloud)';

    public function handle()
    {
        $images = ProductImage::all();

        foreach ($images as $image) {

            $path = $image->path;

            // لو موجود في local
            if (Storage::disk('public')->exists($path)) {

                $this->info("Moving: $path");

                $content = Storage::disk('public')->get($path);

                // رفع على S3 (default disk)
                Storage::put($path, $content);
            } else {
                $this->warn("Not found in local: $path");
            }
        }

        $this->info('Done ✅');
    }
}
