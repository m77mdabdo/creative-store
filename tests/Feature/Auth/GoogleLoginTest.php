<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as GoogleUser;
use Mockery;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'customer']);
        Role::firstOrCreate(['name' => 'owner']);
    }

    private function google(string $email, bool $verified = true, string $id = 'google-123', string $name = 'Google Person'): void
    {
        $user = (new GoogleUser())
            ->setRaw(['sub' => $id, 'email' => $email, 'email_verified' => $verified])
            ->map(['id' => $id, 'email' => $email, 'name' => $name]);

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->andReturn($user);
        $provider->shouldNotReceive('stateless');

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }

    public function test_a_new_verified_google_user_gets_a_customer_account(): void
    {
        $this->google('new.person@gmail.com');

        $this->get(route('google.callback'))->assertRedirect();

        $user = User::where('email', 'new.person@gmail.com')->sole();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('google-123', $user->google_id);
        $this->assertTrue($user->roles()->where('name', 'customer')->exists());
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_an_unverified_google_email_cannot_take_over_an_existing_account(): void
    {
        $owner = User::factory()->create(['email' => 'owner@store.test', 'password' => Hash::make('Owner12345')]);
        $owner->assignRole('owner');

        $this->google('owner@store.test', verified: false, name: 'Attacker');

        $this->get(route('google.callback'))
            ->assertRedirect()
            ->assertSessionHas('error', __('site.google_unverified'));

        $this->assertGuest();
        $owner->refresh();
        $this->assertTrue(Hash::check('Owner12345', $owner->password));
        $this->assertNull($owner->google_id);
        $this->assertNotSame('Attacker', $owner->name);
    }

    public function test_a_verified_google_email_links_without_touching_the_password(): void
    {
        $user = User::factory()->create(['email' => 'sara@gmail.com', 'name' => 'Sara', 'password' => Hash::make('Sara12345')]);

        $this->google('sara@gmail.com', name: 'Someone Else');
        $this->get(route('google.callback'));

        $this->assertAuthenticatedAs($user);
        $user->refresh();
        $this->assertSame('google-123', $user->google_id);
        $this->assertSame('Sara', $user->name);
        $this->assertTrue(Hash::check('Sara12345', $user->password));
    }

    public function test_an_email_linked_to_another_google_account_is_refused(): void
    {
        User::factory()->create(['email' => 'sara@gmail.com', 'google_id' => 'google-original']);

        $this->google('sara@gmail.com', id: 'google-intruder');

        $this->get(route('google.callback'))->assertSessionHas('error', __('site.google_other_account'));
        $this->assertGuest();
    }

    public function test_disabled_and_closed_accounts_cannot_sign_in_with_google(): void
    {
        User::factory()->create(['email' => 'off@gmail.com', 'is_active' => false]);
        $this->google('off@gmail.com');
        $this->get(route('google.callback'))->assertSessionHas('error', __('site.account_disabled'));
        $this->assertGuest();

        $closed = User::factory()->create(['email' => 'closed@gmail.com']);
        $closed->delete();
        $this->google('closed@gmail.com', id: 'google-closed');

        // Used to fail with a duplicate-email database error (500).
        $this->get(route('google.callback'))->assertRedirect()->assertSessionHas('error', __('site.account_deleted'));
        $this->assertGuest();
        $this->assertSame(1, User::withTrashed()->where('email', 'closed@gmail.com')->count());
    }

    public function test_a_failed_or_forged_callback_shows_an_error(): void
    {
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->andThrow(new \Laravel\Socialite\Two\InvalidStateException());
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->get(route('google.callback', ['code' => 'stolen-code']))
            ->assertRedirect()
            ->assertSessionHas('error', __('site.google_failed'));
        $this->assertGuest();
    }
}
