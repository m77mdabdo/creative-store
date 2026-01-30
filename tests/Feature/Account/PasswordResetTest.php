<?php

namespace Tests\Feature\Account;

use App\Mail\SendResetLinkMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private function requestLink(User $user): string
    {
        $token = null;
        Mail::fake();

        $this->post(route('forgot-password.submit'), ['email' => $user->email])->assertSessionHas('success');

        Mail::assertSent(SendResetLinkMail::class, function (SendResetLinkMail $mail) use (&$token) {
            $token = basename((string) parse_url($mail->resetUrl, PHP_URL_PATH));

            return true;
        });

        return $token;
    }

    public function test_reset_tokens_are_stored_hashed_and_work_once(): void
    {
        $user = User::factory()->create();
        $token = $this->requestLink($user);

        $this->assertNotSame($token, DB::table('password_reset_tokens')->where('email', $user->email)->value('token'));

        $this->post(route('password.update'), [
            'email' => $user->email, 'token' => $token, 'password' => 'Fresh12345', 'password_confirmation' => 'Fresh12345',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('Fresh12345', $user->fresh()->password));
        $this->assertSame(0, DB::table('password_reset_tokens')->count());
    }

    public function test_expired_reset_links_are_refused(): void
    {
        $user = User::factory()->create();
        $token = $this->requestLink($user);

        $this->travel(61)->minutes();

        $this->post(route('password.update'), [
            'email' => $user->email, 'token' => $token, 'password' => 'Fresh12345', 'password_confirmation' => 'Fresh12345',
        ])->assertSessionHas('error');

        $this->assertFalse(Hash::check('Fresh12345', $user->fresh()->password));
    }

    public function test_unknown_emails_get_the_same_answer_and_no_mail(): void
    {
        Mail::fake();
        $known = User::factory()->create();

        $forKnown = $this->post(route('forgot-password.submit'), ['email' => $known->email]);
        $forUnknown = $this->post(route('forgot-password.submit'), ['email' => 'nobody-here@example.com']);

        $forUnknown->assertSessionHasNoErrors()->assertSessionHas('success', __('site.reset_link_sent_if_exists'));
        $forKnown->assertSessionHas('success', __('site.reset_link_sent_if_exists'));

        Mail::assertSent(SendResetLinkMail::class, 1);
        $this->assertFalse(DB::table('password_reset_tokens')->where('email', 'nobody-here@example.com')->exists());
    }
}
