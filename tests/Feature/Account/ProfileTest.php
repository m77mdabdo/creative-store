<?php

namespace Tests\Feature\Account;

use App\Mail\ConfirmEmailChangeMail;
use App\Models\User;
use App\Support\Auth\EmailChangeLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(config('filesystems.media_disk', 'public'));
        $this->user = User::factory()->create([
            'name'     => 'Mohamed Abdo',
            'email'    => 'mohamed@example.com',
            'password' => Hash::make('OldPass123'),
            'phone'    => null,
        ]);
    }

    public function test_the_profile_page_shows_the_person_and_their_numbers(): void
    {
        $this->actingAs($this->user)
            ->get(route('myaccount'))
            ->assertOk()
            ->assertSee('Hi, Mohamed')
            ->assertSee('MA')
            ->assertSee('mohamed@example.com');
    }

    public function test_name_phone_and_photo_can_be_updated(): void
    {
        $this->actingAs($this->user)
            ->post(route('myaccount.profile'), [
                'name'   => 'Mohamed A. Abdo',
                'email'  => 'mohamed@example.com',
                'phone'  => '0512345678',
                'avatar' => UploadedFile::fake()->image('me.jpg', 300, 300),
            ])
            ->assertRedirect()
            ->assertSessionHas('success_profile');

        $user = $this->user->fresh();
        $this->assertSame('Mohamed A. Abdo', $user->name);
        $this->assertSame('+966512345678', $user->phone);
        Storage::disk(config('filesystems.media_disk', 'public'))->assertExists($user->avatar_path);

        $old = $user->avatar_path;
        $this->actingAs($user)->post(route('myaccount.profile'), [
            'name' => $user->name, 'email' => $user->email, 'phone' => $user->phone, 'remove_avatar' => 1,
        ]);

        $this->assertNull($user->fresh()->avatar_path);
        Storage::disk(config('filesystems.media_disk', 'public'))->assertMissing($old);
    }

    public function test_changing_the_email_needs_the_current_password_and_a_confirmation(): void
    {
        Mail::fake();
        $payload = ['name' => 'Mohamed Abdo', 'email' => 'new@example.com'];

        $this->actingAs($this->user)
            ->post(route('myaccount.profile'), $payload)
            ->assertSessionHasErrors('current_password');
        $this->assertSame('mohamed@example.com', $this->user->fresh()->email);

        $this->actingAs($this->user)
            ->post(route('myaccount.profile'), $payload + ['current_password' => 'OldPass123'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('info_profile');

        // Nothing changes until the new mailbox confirms it.
        $this->assertSame('mohamed@example.com', $this->user->fresh()->email);

        $link = null;
        Mail::assertSent(ConfirmEmailChangeMail::class, function (ConfirmEmailChangeMail $mail) use (&$link) {
            $link = $mail->confirmUrl;

            return $mail->hasTo('new@example.com');
        });

        $this->actingAs($this->user)->get($link)->assertRedirect(route('myaccount'));
        $this->assertSame('new@example.com', $this->user->fresh()->email);

        // The link works once.
        $this->actingAs($this->user->fresh())->get($link)->assertRedirect(route('login'));
    }

    public function test_a_tampered_email_confirmation_link_is_refused(): void
    {
        $link = EmailChangeLink::for($this->user, 'new@example.com');

        $this->get(str_replace('new%40example.com', 'evil%40example.com', $link))->assertForbidden();
        $this->assertSame('mohamed@example.com', $this->user->fresh()->email);
    }

    public function test_emails_and_phones_stay_unique(): void
    {
        User::factory()->create(['email' => 'taken@example.com', 'phone' => '+966500000001']);

        $this->actingAs($this->user)
            ->post(route('myaccount.profile'), [
                'name' => 'Mohamed', 'email' => 'taken@example.com', 'current_password' => 'OldPass123',
            ])
            ->assertSessionHasErrors('email');

        $this->actingAs($this->user)
            ->post(route('myaccount.profile'), [
                'name' => 'Mohamed', 'email' => 'mohamed@example.com', 'phone' => '0500000001',
            ])
            ->assertSessionHasErrors('phone');
    }

    public function test_password_change_rules(): void
    {
        $this->actingAs($this->user)
            ->post(route('myaccount.password'), ['current_password' => 'OldPass123', 'password' => 'abcdefgh', 'password_confirmation' => 'abcdefgh'])
            ->assertSessionHasErrors('password');

        $this->actingAs($this->user)
            ->post(route('myaccount.password'), ['current_password' => 'wrong', 'password' => 'NewPass456', 'password_confirmation' => 'NewPass456'])
            ->assertSessionHasErrors('current_password');

        $this->actingAs($this->user)
            ->post(route('myaccount.password'), ['current_password' => 'OldPass123', 'password' => 'NewPass456', 'password_confirmation' => 'NewPass456'])
            ->assertSessionHas('success_password');

        $this->assertTrue(Hash::check('NewPass456', $this->user->fresh()->password));
    }
}
