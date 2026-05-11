<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_sign_up_refuses_short_or_letters_only_passwords(): void
    {
        foreach (['abc12', 'onlyletters'] as $weak) {
            $this->post(route('register.submit'), [
                'name' => 'New Person', 'email' => 'new@example.com',
                'password' => $weak, 'password_confirmation' => $weak,
            ])->assertSessionHasErrors('password');
        }
    }
}
