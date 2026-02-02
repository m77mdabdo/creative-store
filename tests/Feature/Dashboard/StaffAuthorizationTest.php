<?php

namespace Tests\Feature\Dashboard;

use App\Models\Product;
use App\Models\Review;
use App\Models\Role;
use App\Models\User;
use App\Notifications\StaffInvitationNotification;
use App\Services\Pages\PageImageService;
use Database\Seeders\AclSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(AclSeeder::class);
        $this->owner = User::factory()->create();
        $this->owner->assignRole('owner');
    }

    private function staff(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function roleId(string $name): int
    {
        return (int) Role::where('name', $name)->value('id');
    }

    public function test_an_admin_cannot_lift_a_deny_the_owner_set_on_them(): void
    {
        $admin = $this->staff('admin');
        $admin->denyPermission('audit.view');

        $this->actingAs($admin)->put(route('dashboard.users.update', $admin), [
            'name' => $admin->name, 'email' => $admin->email,
            'roles' => [$this->roleId('admin')], 'direct_permissions' => [], 'denied_permissions' => [],
        ]);

        $this->assertFalse($admin->fresh()->hasPermission('audit.view'));
    }

    public function test_staff_cannot_hand_out_roles_or_permissions_they_lack(): void
    {
        $employee = $this->staff('employee');
        $employee->givePermission('users.edit');
        $target = $this->staff('employee');

        $this->actingAs($employee)->put(route('dashboard.users.update', $target), [
            'name' => $target->name, 'email' => $target->email, 'roles' => [$this->roleId('admin')],
        ])->assertSessionHasErrors('roles');
        $this->assertFalse($target->fresh()->hasRole('admin'));

        $this->actingAs($employee)->put(route('dashboard.users.update', $target), [
            'name' => $target->name, 'email' => $target->email,
            'roles' => [$this->roleId('employee')], 'direct_permissions' => ['backups.view'],
        ])->assertSessionHasErrors('direct_permissions');
        $this->assertFalse($target->fresh()->hasPermission('backups.view'));

        // Editing your own roles is ignored for non-owners.
        $this->actingAs($employee)->put(route('dashboard.users.update', $employee), [
            'name' => 'Renamed', 'email' => $employee->email, 'roles' => [$this->roleId('admin')],
        ]);
        $this->assertSame('Renamed', $employee->fresh()->name);
        $this->assertFalse($employee->fresh()->hasRole('admin'));
    }

    public function test_roles_can_only_carry_permissions_the_editor_holds(): void
    {
        $admin = $this->staff('admin');

        $this->actingAs($admin)->post(route('dashboard.roles.store'), [
            'name' => 'backup-reader', 'permissions' => ['backups.view'],
        ])->assertSessionHasErrors('permissions');

        $this->assertDatabaseMissing('roles', ['name' => 'backup-reader']);
    }

    public function test_only_owners_can_send_invitations_to_owners(): void
    {
        Notification::fake();
        $admin = $this->staff('admin');

        $this->actingAs($admin)
            ->post(route('dashboard.users.invitation', $this->owner))
            ->assertSessionHas('error', __('staff.invite.owner_only'));

        Notification::assertNothingSent();
        $this->actingAs($this->owner)->post(route('dashboard.users.invitation', $admin))->assertSessionHas('success');
        Notification::assertSentTo($admin, StaffInvitationNotification::class);
    }

    public function test_bulk_deletes_need_the_delete_permission(): void
    {
        $employee = $this->staff('employee');
        $review = Review::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($employee)
            ->post(route('dashboard.reviews.bulk-action'), ['action' => 'delete', 'ids' => [$review->id]])
            ->assertForbidden();
        $this->actingAs($employee)
            ->post(route('dashboard.products.bulk-action'), ['action' => 'delete', 'ids' => [$product->id]])
            ->assertForbidden();

        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
        $this->assertNotSoftDeleted($product);
    }

    public function test_backups_need_backup_permissions_not_audit_access(): void
    {
        $admin = $this->staff('admin');
        $this->assertTrue($admin->hasPermission('audit.view'));

        $this->actingAs($admin)->get(route('dashboard.backups.index'))->assertForbidden();
        $this->actingAs($admin)->post(route('dashboard.backups.create'))->assertForbidden();
        $this->actingAs($admin)->get(route('dashboard.error-logs.index'))->assertOk();
    }

    public function test_page_images_only_delete_files_the_pages_feature_stored(): void
    {
        Storage::fake(config('filesystems.media_disk', 'public'));
        $disk = Storage::disk(config('filesystems.media_disk', 'public'));
        $disk->put('products/general/victim.jpg', 'x');
        $disk->put('pages/about/old.jpg', 'x');
        $service = app(PageImageService::class);

        $service->upload(UploadedFile::fake()->image('a.jpg'), 'products/general/victim.jpg');
        $service->deleteIfExists('pages/../products/general/victim.jpg');
        $disk->assertExists('products/general/victim.jpg');

        $service->upload(UploadedFile::fake()->image('b.jpg'), 'pages/about/old.jpg');
        $disk->assertMissing('pages/about/old.jpg');
    }
}
