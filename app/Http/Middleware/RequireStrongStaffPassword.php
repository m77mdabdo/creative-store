<?php

namespace App\Http\Middleware;

use App\Support\Auth\StaffPassword;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps a staff member out of the dashboard until a password that was
 * flagged as weak at sign-in has been changed on the account page.
 */
class RequireStrongStaffPassword
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get(StaffPassword::UPGRADE_FLAG)) {
            return $next($request);
        }

        $message = __('account.password_upgrade_required');

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 403);
        }

        return redirect()->route('myaccount')->with('password_upgrade', $message);
    }
}
