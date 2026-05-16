<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Database\Seeders\AuthSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SeedingTest extends TestCase
{
    use RefreshDatabase;

    private function setEnv(string $key, ?string $value): void
    {
        if ($value === null) {
            putenv($key);
            unset($_ENV[$key], $_SERVER[$key]);

            return;
        }

        putenv("{$key}={$value}");
        $_ENV[$key] = $_SERVER[$key] = $value;
    }

    protected function tearDown(): void
    {
        $this->setEnv('SEED_OWNER_EMAIL', null);
        $this->setEnv('SEED_DEMO_ACCOUNTS', null);

        parent::tearDown();
    }

    public function test_production_seeding_creates_no_accounts_with_known_passwords(): void
    {
        $this->app['env'] = 'production';
        $this->setEnv('SEED_OWNER_EMAIL', 'Owner@Store.test');

        // Run directly: db:seed asks for confirmation in production.
        $this->app->make(AuthSeeder::class)->run();

        $owner = User::query()->sole();
        $this->assertSame('owner@store.test', $owner->email);
        $this->assertTrue($owner->hasRole('owner'));
        $this->assertFalse(Hash::check('password', $owner->password));
    }

    public function test_production_seeding_without_an_owner_email_creates_nobody(): void
    {
        $this->app['env'] = 'production';

        // Run directly: db:seed asks for confirmation in production.
        $this->app->make(AuthSeeder::class)->run();

        $this->assertSame(0, User::query()->count());
    }
}
