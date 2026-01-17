<?php

return [
    'roles' => [
        'owner' => 'Owner',
        'admin' => 'Admin',
        'employee' => 'Employee',
        'customer' => 'Customer',
    ],

    'invite' => [
        'subject' => 'Welcome to the :store team',
        'title' => 'Welcome to the team, :name!',
        'intro' => 'You have been added to the Creative Store dashboard as :role.',
        'intro_by' => ':by added you to the Creative Store dashboard as :role.',
        'details' => 'Your sign-in details',
        'login_page' => 'Sign-in page',
        'email' => 'Email',
        'password' => 'Password',
        'password_hint' => 'Choose your own with the button below, or use the one your manager gave you.',
        'button' => 'Set my password and sign in',
        'expiry' => 'The button works once and is valid for :days days. After that, use "Forgot password" on the sign-in page.',
        'ignore' => 'If you were not expecting this, you can ignore this email.',
        'text' => 'Creative Store: hi :name, you were added to the dashboard as :role. Set your password and sign in: :link',
        'sent' => 'An invitation with the sign-in details was sent to :email.',
        'send_failed' => 'The user was created, but the invitation could not be sent. Use "Resend invitation" to try again.',
        'resend' => 'Resend invitation',
        'not_staff' => 'Invitations are only for team members (owner, admin or employee).',
    ],

    'welcome' => [
        'heading' => 'Welcome, :name',
        'intro' => 'Choose a password for your Creative Store dashboard account.',
        'button' => 'Save password and sign in',
        'done' => 'Welcome aboard, :name! Your password is set.',
        'expired' => 'This invitation link has expired or was already used. Sign in, or use "Forgot password" to get a new link.',
    ],

    'bell' => [
        'invited_title' => 'Welcome to the team',
        'invited_message' => 'You were added as :role. Explore your dashboard.',
        'added_title' => 'New team member',
        'added_message' => ':name joined as :role',
        'added_message_by' => ':by added :name as :role',
    ],

    'ceiling' => [
        'own_account' => 'You cannot change the roles or permissions on your own account. Ask an owner.',
        'role' => 'You can only give roles whose permissions you hold yourself: :role.',
        'permission' => 'You can only grant permissions you hold yourself: :permissions.',
    ],
];
