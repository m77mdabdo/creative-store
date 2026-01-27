<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Backups and error logs used to be reachable with the read-only "View
 * audit logs" permission. They now have their own permissions. Roles that
 * could see audit logs keep error-log access; backup access (the whole
 * database) stays with owners until an owner grants it.
 */
return new class extends Migration
{
    private const CODES = [
        'error-logs.view' => ['View error logs', 'error-logs'],
        'error-logs.edit' => ['Resolve and clear error logs', 'error-logs'],
        'backups.view'    => ['View and download backups', 'backups'],
        'backups.create'  => ['Create backups', 'backups'],
        'backups.delete'  => ['Delete backups', 'backups'],
    ];

    public function up(): void
    {
        $hasGroup = Schema::hasColumn('permissions', 'group');

        foreach (self::CODES as $code => [$name, $group]) {
            if (DB::table('permissions')->where('code', $code)->exists()) {
                continue;
            }

            DB::table('permissions')->insert(array_filter([
                'code'       => $code,
                'name'       => $name,
                'group'      => $hasGroup ? $group : null,
                'created_at' => now(),
                'updated_at' => now(),
            ], fn ($value) => $value !== null));
        }

        $auditId = DB::table('permissions')->where('code', 'audit.view')->value('id');

        if (! $auditId) {
            return;
        }

        $errorLogIds = DB::table('permissions')->whereIn('code', ['error-logs.view', 'error-logs.edit'])->pluck('id');
        $roleIds = DB::table('permission_role')->where('permission_id', $auditId)->pluck('role_id');

        foreach ($roleIds as $roleId) {
            foreach ($errorLogIds as $permissionId) {
                $exists = DB::table('permission_role')
                    ->where('role_id', $roleId)
                    ->where('permission_id', $permissionId)
                    ->exists();

                if (! $exists) {
                    DB::table('permission_role')->insert(['role_id' => $roleId, 'permission_id' => $permissionId]);
                }
            }
        }
    }

    public function down(): void
    {
        // Permissions are left in place; removing them would drop grants owners made.
    }
};
