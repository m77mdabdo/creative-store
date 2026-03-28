<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissionNames = [
            'blog-categories.view',
            'blog-categories.create',
            'blog-categories.edit',
            'blog-categories.delete',
            'blogs.view',
            'blogs.create',
            'blogs.edit',
            'blogs.delete',
        ];

        foreach ($permissionNames as $permissionName) {
            $code = Str::upper(
                str_replace(['.', '-'], '_', $permissionName)
            );

            Permission::firstOrCreate(
                ['name' => $permissionName],
                ['code' => $code]
            );
        }
    }
}
