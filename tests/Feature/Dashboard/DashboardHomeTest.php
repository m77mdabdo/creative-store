<?php

namespace Tests\Feature\Dashboard;

use App\Models\AuditLog;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\AclSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardHomeTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(AclSeeder::class);
        $this->customer = User::factory()->create(['name' => 'Private Customer Name']);
        Order::factory()->create(['user_id' => $this->customer->id]);
        AuditLog::create(['user_id' => $this->customer->id, 'action' => 'secret-audit-action', 'auditable_type' => User::class, 'auditable_id' => $this->customer->id]);
    }

    public function test_staff_without_section_permissions_see_no_customer_or_audit_data(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('employee');
        $staff->denyPermission('orders.view');

        $this->actingAs($staff)->get(route('dashboard.home'))
            ->assertOk()
            ->assertDontSee('Private Customer Name')
            ->assertDontSee(__('site.Latest Orders'))
            ->assertDontSee(__('site.Recent Activity'))
            ->assertDontSee(__('site.Customers Count'));
    }

    public function test_the_owner_sees_every_panel(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $this->actingAs($owner)->get(route('dashboard.home'))
            ->assertOk()
            ->assertSee(__('site.Latest Orders'))
            ->assertSee(__('site.Recent Activity'))
            ->assertSee(__('site.Customers Count'));
    }
}
