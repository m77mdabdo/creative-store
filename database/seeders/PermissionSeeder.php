<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'blog-categories.view',
            'blog-categories.create',
            'blog-categories.edit',
            'blog-categories.delete',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                [
                    'code' => Str::upper(Str::replace(['.', '-'], '_', $permissionName)),
                ]
            );
        }
    }
}
