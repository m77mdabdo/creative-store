<?php

namespace Tests\Feature\Dashboard;

use App\Models\Role;
use App\Models\User;
use App\Notifications\StaffInvitationNotification;
use App\Notifications\StaffMemberAddedNotification;
use App\Support\Auth\StaffWelcomeLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaffInvitationTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        config(['mail.default' => 'array']);

        foreach (['owner', 'admin', 'employee', 'customer'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $this->owner = User::factory()->create(['name' => 'Mohamed Owner', 'is_active' => true]);
        $this->owner->assignRole('owner');
    }

    private function sentEmails(): array
    {
        return array_values(iterator_to_array(app('mail.manager')->mailer('array')->getSymfonyTransport()->messages()));
    }

    private function createUser(string $role, array $attributes = [])
    {
        return $this->actingAs($this->owner)->post(route('dashboard.users.store'), array_merge([
            'name'                  => 'Sara Admin',
            'email'                 => 'sara@example.com',
            'phone'                 => null,
            'password'              => 'TempPass123',
            'password_confirmation' => 'TempPass123',
            'is_active'             => 1,
            'roles'                 => [Role::where('name', $role)->value('id')],
        ], $attributes));
    }

    public function test_a_new_admin_gets_an_invitation_and_the_team_is_told(): void
    {
        $otherAdmin = User::factory()->create(['is_active' => true]);
        $otherAdmin->assignRole('admin');

        $this->createUser('admin')
            ->assertRedirect(route('dashboard.users.index'))
            ->assertSessionHas('info');

        $sara = User::where('email', 'sara@example.com')->sole();

        $emails = $this->sentEmails();
        $this->assertCount(1, $emails);
        $this->assertSame('sara@example.com', $emails[0]->getEnvelope()->getRecipients()[0]->getAddress());
        $body = $emails[0]->getOriginalMessage()->getHtmlBody();
        $this->assertStringContainsString('/welcome/' . $sara->id, html_entity_decode($body));
        $this->assertStringContainsString(route('login'), html_entity_decode($body));
        $this->assertStringContainsString('Admin', $body);
        $this->assertStringNotContainsString('TempPass123', $body);

        $this->assertSame(StaffInvitationNotification::class, $sara->notifications()->sole()->type);
        $this->assertSame(StaffMemberAddedNotification::class, $otherAdmin->notifications()->sole()->type);
        $this->assertSame(StaffMemberAddedNotification::class, $this->owner->notifications()->sole()->type);
    }

    public function test_customers_get_no_staff_invitation(): void
    {
        $this->createUser('customer', ['email' => 'buyer@example.com'])->assertSessionMissing('info');

        $this->assertCount(0, $this->sentEmails());
        $this->assertSame(0, $this->owner->notifications()->count());
    }

    public function test_the_welcome_link_sets_a_password_once_and_signs_in(): void
    {
        $this->createUser('employee');
        $sara = User::where('email', 'sara@example.com')->sole();
        $link = StaffWelcomeLink::for($sara);
        Auth::logout();

        $this->get($link)->assertOk()->assertSee('sara@example.com');

        $this->post($link, ['password' => 'short', 'password_confirmation' => 'short'])
            ->assertSessionHasErrors('password');

        $this->post($link, ['password' => 'MyOwnPass2026', 'password_confirmation' => 'MyOwnPass2026'])
            ->assertRedirect(route('dashboard.home'));

        $this->assertAuthenticatedAs($sara);
        $this->assertTrue(Hash::check('MyOwnPass2026', $sara->fresh()->password));
        $this->assertNotNull($sara->fresh()->email_verified_at);

        Auth::logout();
        $this->get($link)->assertRedirect(route('login'))->assertSessionHas('info');

        $this->get($link . 'x')->assertForbidden();
    }

    public function test_invitations_can_be_sent_again(): void
    {
        $this->createUser('admin');
        $sara = User::where('email', 'sara@example.com')->sole();

        $this->actingAs($this->owner)
            ->from(route('dashboard.users.show', $sara))
            ->post(route('dashboard.users.invitation', $sara))
            ->assertRedirect(route('dashboard.users.show', $sara))
            ->assertSessionHas('success');

        $this->assertCount(2, $this->sentEmails());

        $customer = User::factory()->create();
        $customer->assignRole('customer');
        $this->actingAs($this->owner)
            ->post(route('dashboard.users.invitation', $customer))
            ->assertSessionHas('error');
    }
}
