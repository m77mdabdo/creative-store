<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\StoreContactMessageRequest;
use App\Jobs\SendContactNotificationsJob;
use App\Models\ContactMessage;
use App\Models\Page;
use App\Support\PageContent\ContactPageContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        $locale = app()->getLocale();

        $payload = Cache::remember("page.contact.{$locale}", now()->addMinutes(30), function () use ($locale) {
            $page = Page::query()
                ->where('slug', 'contact')
                ->where('is_active', true)
                ->with('translations')
                ->first();

            if (! $page) {
                return [
                    'title' => 'Contact',
                    'content' => [],
                ];
            }

            $translation = $page->translate($locale, true);

            return [
                'title' => $translation?->title ?? 'Contact',
                'content' => ContactPageContent::decode($translation?->content ?? null),
            ];
        });

        return view('site.contact.contact', [
            'pageTitle' => $payload['title'] ?? 'Contact',
            'content' => $payload['content'] ?? [],
        ]);
    }

    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        $contactMessage = DB::transaction(function () use ($request) {
            return ContactMessage::query()->create([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'phone' => $request->validated('phone'),
                'subject' => $request->validated('subject'),
                'message' => $request->validated('message'),
                'is_read' => false,
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 1000),
            ]);
        });

        SendContactNotificationsJob::dispatch($contactMessage->id);

        return back()->with('success', 'Your message has been sent successfully.');
    }
}
