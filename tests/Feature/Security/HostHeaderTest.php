<?php

namespace Tests\Feature\Security;

use App\Mail\SendResetLinkMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class HostHeaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_links_use_the_application_url_not_the_request_host(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $this->withHeader('Host', 'attacker.test')
            ->post(route('forgot-password.submit'), ['email' => $user->email]);

        Mail::assertSent(SendResetLinkMail::class, function (SendResetLinkMail $mail) {
            return str_starts_with($mail->resetUrl, rtrim(config('app.url'), '/') . '/')
                && ! str_contains($mail->resetUrl, 'attacker.test');
        });
    }

    public function test_a_forged_host_cannot_poison_the_cached_sitemap(): void
    {
        Cache::flush();

        $this->withHeader('Host', 'attacker.test')->get('/sitemap.xml')->assertOk();

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertDontSee('attacker.test', false);
    }
}
