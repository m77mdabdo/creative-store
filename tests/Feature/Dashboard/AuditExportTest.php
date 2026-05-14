<?php

namespace Tests\Feature\Dashboard;

use App\Models\AuditLog;
use App\Models\User;
use Database\Seeders\AclSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_names_that_look_like_formulas_are_exported_as_text(): void
    {
        $this->seed(AclSeeder::class);
        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $customer = User::factory()->create(['name' => '=HYPERLINK("http://evil.test","open")']);
        AuditLog::create(['user_id' => $customer->id, 'action' => 'updated', 'auditable_type' => User::class, 'auditable_id' => $customer->id]);

        $csv = $this->actingAs($owner)->get(route('dashboard.audit.export'))->assertOk()->streamedContent();

        $this->assertStringContainsString("'=HYPERLINK", $csv);
        $this->assertStringNotContainsString(',"=HYPERLINK', $csv);
    }
}
