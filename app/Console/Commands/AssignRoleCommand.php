<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

/**
 * Grants a role to an existing user, e.g. to give a team member dashboard access.
 *
 *   php artisan users:assign-role someone@example.com owner
 */
class AssignRoleCommand extends Command
{
    protected $signature = 'users:assign-role {email : Email of an existing user} {role=owner : owner, admin, employee or customer}';

    protected $description = 'Give an existing user a role';

    private const ROLES = ['owner', 'admin', 'employee', 'customer'];

    public function handle(): int
    {
        $role = strtolower((string) $this->argument('role'));

        if (! in_array($role, self::ROLES, true)) {
            $this->error('Role must be one of: ' . implode(', ', self::ROLES));

            return self::INVALID;
        }

        $user = User::query()->where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('No user with that email. Register the account first.');

            return self::FAILURE;
        }

        Role::query()->firstOrCreate(['name' => $role]);
        $user->assignRole($role);
        $user->forceFill(['is_active' => true])->save();

        $this->info("{$user->email} now has the {$role} role.");

        return self::SUCCESS;
    }
}
