<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Support\Auth\StaffPassword;
use Database\Seeders\AclSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaffPasswordUpgradeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(AclSeeder::class);
    }

    private function staff(string $password, string $role = 'admin'): User
    {
        $user = User::factory()->create([
            'email'    => $role . '@example.test',
            'password' => Hash::make($password),
        ]);
        $user->assignRole($role);

        return $user;
    }

    private function login(User $user, string $password)
    {
        return $this->post(route('login.submit'), ['email' => $user->email, 'password' => $password]);
    }

    public function test_staff_with_a_weak_password_are_sent_to_change_it(): void
    {
        $admin = $this->staff('weakpass1');

        $this->login($admin, 'weakpass1');

        $this->assertAuthenticatedAs($admin);
        $this->get(route('dashboard.home'))
            ->assertRedirect(route('myaccount'))
            ->assertSessionHas('password_upgrade');

        $this->getJson(route('dashboard.home'))->assertForbidden();
    }

    public function test_the_new_password_must_meet_the_staff_rule_and_unlocks_the_dashboard(): void
    {
        $admin = $this->staff('weakpass1');
        $this->login($admin, 'weakpass1');

        $this->post(route('myaccount.password'), [
            'current_password'      => 'weakpass1',
            'password'              => 'stillweak2',
            'password_confirmation' => 'stillweak2',
        ])->assertSessionHasErrors('password');

        $this->assertTrue(session()->has(StaffPassword::UPGRADE_FLAG));

        $this->post(route('myaccount.password'), [
            'current_password'      => 'weakpass1',
            'password'              => 'Strong-Pass-2026',
            'password_confirmation' => 'Strong-Pass-2026',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('Strong-Pass-2026', $admin->fresh()->password));
        $this->get(route('dashboard.home'))->assertOk();
    }

    public function test_staff_with_a_strong_password_go_straight_in(): void
    {
        $owner = $this->staff('Owner-Pass-2026', 'owner');

        $this->login($owner, 'Owner-Pass-2026')->assertRedirect(route('dashboard.home'));

        $this->get(route('dashboard.home'))->assertOk();
    }

    public function test_customers_keep_the_customer_password_rule(): void
    {
        $customer = User::factory()->create(['password' => Hash::make('simple123')]);

        $this->login($customer, 'simple123')->assertRedirect(route('home'));
        $this->assertFalse(session()->has(StaffPassword::UPGRADE_FLAG));

        $this->post(route('myaccount.password'), [
            'current_password'      => 'simple123',
            'password'              => 'another12',
            'password_confirmation' => 'another12',
        ])->assertSessionHasNoErrors();
    }
}
