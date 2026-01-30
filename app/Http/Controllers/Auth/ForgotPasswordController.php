<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Mail\SendResetLinkMail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function __invoke(ForgotPasswordRequest $request)
    {
        $email = Str::lower(trim((string) $request->email));

        // The answer is the same whether or not the address has an account,
        // so this form cannot be used to find out who shops here.
        if (User::query()->where('email', $email)->exists()) {
            $token = Str::random(60);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                // Only a hash is stored, so a leaked table cannot be used to reset passwords.
                ['token' => hash('sha256', $token), 'created_at' => now()]
            );

            Mail::to($email)->send(new SendResetLinkMail($token, $email));
        }

        return back()->with('success', __('site.reset_link_sent_if_exists'));
    }
}
