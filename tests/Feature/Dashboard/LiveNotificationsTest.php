<?php

namespace Tests\Feature\Dashboard;

use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use App\Notifications\OrderPlacedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class LiveNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        $this->admin = User::factory()->create(['is_active' => true]);
        $this->admin->assignRole('admin');
    }

    public function test_the_bell_entry_is_written_at_once_even_when_mail_waits_for_the_queue(): void
    {
        config(['queue.default' => 'database']);
        $order = Order::factory()->create(['user_id' => User::factory(), 'status' => 'pending', 'coupon_id' => null]);

        $this->admin->notify(new OrderPlacedNotification($order));

        $this->assertSame(1, $this->admin->notifications()->count());
        $this->assertSame(1, DB::table('jobs')->count()); // the email is still queued
    }

    public function test_the_feed_reports_counts_rows_and_what_is_new(): void
    {
        Order::factory()->count(2)->create(['user_id' => null, 'status' => 'pending', 'coupon_id' => null]);
        ContactMessage::create(['name' => 'Huda', 'email' => 'huda@example.com', 'subject' => 'Hello', 'message' => 'Do you ship to Abha?', 'is_read' => false]);

        $since = now()->subSecond()->getTimestamp();
        $this->admin->notifications()->create([
            'id'   => (string) Str::uuid(),
            'type' => 'custom',
            'data' => ['title' => 'Stock running low', 'message' => 'Linen Shirt has 2 left'],
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson(route('dashboard.notifications.feed', ['since' => $since]))
            ->assertOk()
            ->assertJson([
                'unread'          => 1,
                'pending_orders'  => 2,
                'unread_messages' => 1,
            ]);

        $this->assertSame('Stock running low', $response->json('fresh.0.title'));
        $this->assertStringContainsString('Linen Shirt has 2 left', $response->json('html'));

        // Asking again later returns nothing new.
        $this->actingAs($this->admin)
            ->getJson(route('dashboard.notifications.feed', ['since' => now()->addMinute()->getTimestamp()]))
            ->assertJsonCount(0, 'fresh');
    }

    public function test_customers_cannot_read_the_dashboard_feed(): void
    {
        $this->actingAs(User::factory()->create(['is_active' => true]))
            ->getJson(route('dashboard.notifications.feed'))
            ->assertStatus(403);
    }
}
