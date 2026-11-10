<?php

namespace Tests\Feature\Cart;

use App\Jobs\ProcessAbandonedCarts;
use App\Models\AbandonedCartRecovery;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\BuildsShop;
use Tests\TestCase;

class CartReminderTest extends TestCase
{
    use BuildsShop;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake(['el.cloud.unifonic.com/*' => Http::response(['success' => true])]);
        config([
            'store.cart_reminders' => [10, 60, 120, 300],
            'messaging.driver' => 'unifonic',
            'messaging.drivers.unifonic.app_sid' => 'test-app-sid',
            'mail.default' => 'array',
        ]);
    }

    private function cartFor(User $user, int $minutesAgo): Cart
    {
        $product = $this->product(150);
        $variant = $this->variant($product);

        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create(['product_id' => $product->id, 'variant_id' => $variant->id, 'quantity' => 2, 'price_snapshot' => 150, 'tax_rate_snapshot' => 0]);
        Cart::whereKey($cart->id)->update(['updated_at' => now()->subMinutes($minutesAgo)]);

        return $cart;
    }

    public function test_customers_who_left_items_get_an_email_and_a_text_after_ten_minutes(): void
    {
        $user = $this->customer(['phone' => '0512345678']);
        $this->cartFor($user, minutesAgo: 15);

        (new ProcessAbandonedCarts())->handle();
        (new ProcessAbandonedCarts())->handle();

        $sent = $this->sentEmails();
        $this->assertCount(1, $sent);
        $this->assertSame($user->email, $sent[0]->getEnvelope()->getRecipients()[0]->getAddress());
        $this->assertStringContainsString('/cart/recover/', $sent[0]->getOriginalMessage()->getHtmlBody());
        Http::assertSentCount(1);
        Http::assertSent(fn (Request $request) => $request['Recipient'] === '966512345678'
            && str_contains($request['Body'], '/cart/recover/'));

        $this->assertSame('sent', AbandonedCartRecovery::sole()->status);
    }

    public function test_recent_or_empty_carts_are_left_alone(): void
    {
        $this->cartFor($this->customer(), minutesAgo: 5);
        Cart::create(['user_id' => $this->customer()->id]);

        (new ProcessAbandonedCarts())->handle();

        $this->assertCount(0, $this->sentEmails());
        $this->assertSame(0, AbandonedCartRecovery::count());
    }

    public function test_the_reminder_link_restores_the_cart_on_any_device(): void
    {
        $user = $this->customer();
        $this->cartFor($user, minutesAgo: 15);
        (new ProcessAbandonedCarts())->handle();
        $link = route('cart.recover', AbandonedCartRecovery::sole()->recovery_token);

        // Signed out: sign in first, then come straight back to the link.
        $this->get($link)->assertRedirect(route('login'));
        $this->assertSame($link, session('url.intended'));

        $this->actingAs($user)->get($link)->assertRedirect(route('cart.index'));
        $this->assertCount(1, session('cart'));
        $this->assertSame(2, array_values(session('cart'))[0]['quantity']);
        $this->assertSame('recovered', AbandonedCartRecovery::sole()->status);

        $this->actingAs($user)->get(route('cart.index'))->assertOk();
    }

    public function test_reminders_follow_the_schedule_and_then_stop(): void
    {
        $user = $this->customer(['phone' => '0512345678']);
        $this->cartFor($user, minutesAgo: 11);
        $run = fn () => (new ProcessAbandonedCarts())->handle();

        $run();
        $this->travel(30)->minutes();
        $run();
        $this->assertCount(1, $this->sentEmails());

        $this->travel(31)->minutes();   // an hour after the first
        $run();
        $this->travel(2)->hours();
        $run();
        $this->travel(5)->hours();
        $run();
        $this->travel(1)->day();
        $run();

        $subjects = array_map(fn ($email) => $email->getOriginalMessage()->getSubject(), $this->sentEmails());
        $this->assertSame([
            'Items are still waiting in your cart',
            'Your cart is still saved for you',
            'Your cart is still saved for you',
            'Last reminder: your cart is about to expire',
        ], $subjects);
        $this->assertSame([1, 2, 3, 4], AbandonedCartRecovery::orderBy('id')->get()->map(fn ($r) => $r->meta['step'])->all());
    }

    public function test_changing_the_cart_starts_over_and_recovering_it_stops_reminders(): void
    {
        $user = $this->customer();
        $cart = $this->cartFor($user, minutesAgo: 15);
        $run = fn () => (new ProcessAbandonedCarts())->handle();

        $run();
        $this->travel(5)->minutes();
        Cart::whereKey($cart->id)->update(['updated_at' => now()]); // the customer changed the cart

        $this->travel(11)->minutes();
        $run();
        $this->assertSame([1, 1], AbandonedCartRecovery::orderBy('id')->get()->map(fn ($r) => $r->meta['step'])->all());

        AbandonedCartRecovery::query()->latest('id')->first()->update(['status' => 'recovered']);
        $this->travel(3)->hours();
        $run();
        $this->assertSame(2, AbandonedCartRecovery::count());
    }

    private function sentEmails(): array
    {
        return array_values(iterator_to_array(app('mail.manager')->mailer('array')->getSymfonyTransport()->messages()));
    }

    public function test_the_command_runs(): void
    {
        $this->artisan('carts:process-abandoned')->assertSuccessful();
    }
}
