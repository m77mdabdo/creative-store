<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function __invoke(ResetPasswordRequest $request)
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        $valid = $record
            && hash_equals((string) $record->token, hash('sha256', (string) $request->token))
            && now()->subMinutes((int) config('auth.passwords.users.expire', 60))->lessThanOrEqualTo($record->created_at);

        if (! $valid) {
            return back()->with('error', __('Invalid Token or email address'));
        }

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        $user = User::where('email', $request->email)->firstOrFail();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', __('Password reset successfully, you can login now'));
    }
}
