<?php

namespace App\Http\Controllers\Newsletter;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
        ]);

        $email = strtolower(trim($validated['email']));

        // updateOrCreate on the unique email: subscribing twice is harmless and
        // re-subscribing clears an earlier opt-out.
        NewsletterSubscriber::query()->updateOrCreate(
            ['email' => $email],
            [
                'locale'          => app()->getLocale(),
                'user_id'         => $request->user()?->id,
                'ip_address'      => $request->ip(),
                'unsubscribed_at' => null,
            ],
        );

        return back()->with('success', __('site.newsletter_subscribed'));
    }
}
