<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Mail\SendResetLinkMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function __invoke(ForgotPasswordRequest $request)
    {
        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            // Only a hash is stored, so a leaked table cannot be used to reset passwords.
            ['token' => hash('sha256', $token), 'created_at' => now()]
        );

        Mail::to($request->email)->send(
            new SendResetLinkMail($token, $request->email)
        );

        return back()->with('success', __('We have sent you an email with the reset link'));
    }
}
