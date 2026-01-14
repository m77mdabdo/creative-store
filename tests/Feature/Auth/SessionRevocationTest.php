<?php

namespace Tests\Feature\Auth;

use App\Models\Product;
use App\Models\User;
use App\Services\Auth\AccountSessions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SessionRevocationTest extends TestCase
{
    use RefreshDatabase;

    private function storeSession(User $user, string $id): void
    {
        DB::table('sessions')->insert([
            'id' => $id, 'user_id' => $user->id, 'ip_address' => '127.0.0.1',
            'user_agent' => 'test', 'payload' => base64_encode('x'), 'last_activity' => time(),
        ]);
    }

    public function test_a_password_reset_signs_out_every_other_session(): void
    {
        config(['session.driver' => 'database']);
        Mail::fake();
        $user = User::factory()->create(['remember_token' => 'old-remember-token']);
        $this->storeSession($user, 'attacker-session');

        $token = null;
        $this->post(route('forgot-password.submit'), ['email' => $user->email]);
        Mail::assertSent(\App\Mail\SendResetLinkMail::class, function ($mail) use (&$token) {
            $token = basename((string) parse_url($mail->resetUrl, PHP_URL_PATH));

            return true;
        });

        $this->post(route('password.update'), [
            'email' => $user->email, 'token' => $token,
            'password' => 'Fresh12345', 'password_confirmation' => 'Fresh12345',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseMissing('sessions', ['id' => 'attacker-session']);
        $this->assertNotSame('old-remember-token', $user->fresh()->getRememberToken());
    }

    public function test_changing_the_password_keeps_only_the_current_session(): void
    {
        config(['session.driver' => 'database']);
        $user = User::factory()->create(['password' => Hash::make('OldPass123'), 'remember_token' => 'old']);
        $this->storeSession($user, 'other-device');

        app(AccountSessions::class)->revokeOthers($user, 'this-device');

        $this->assertDatabaseMissing('sessions', ['id' => 'other-device']);
        $this->assertNotSame('old', $user->fresh()->getRememberToken());
    }

    public function test_a_disabled_customer_is_signed_out_on_the_next_request(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('myaccount'))->assertOk();

        $user->forceFill(['is_active' => false])->save();

        $this->actingAs($user->fresh())
            ->get(route('myaccount'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error', __('site.account_disabled'));

        $this->assertGuest();
    }

    public function test_a_disabled_customer_cannot_post_reviews(): void
    {
        $user = User::factory()->create(['is_active' => false]);
        $product = Product::factory()->create(['status' => 'active']);

        $this->actingAs($user)
            ->post(route('reviews.store'), ['product_id' => $product->id, 'rating' => 5, 'comment' => 'Nice'])
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('reviews', 0);
    }
}
