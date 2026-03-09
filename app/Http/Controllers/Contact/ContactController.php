<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use App\Mail\ContactAutoReplyMail;
use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\NewContactMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('site.contact.contact');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10'],
        ]);

        $contactMessage = ContactMessage::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            'is_read' => false,
        ]);

        // ابعت notification + mail للأدمن
        $admins = User::query()->get(); // غيرها لاحقًا حسب نظام الأدمن عندك

        foreach ($admins as $admin) {
            $admin->notify(new NewContactMessageNotification($contactMessage));
        }

        // Auto reply للعميل
        Mail::to($contactMessage->email)->send(new ContactAutoReplyMail($contactMessage));

        return back()->with('success', 'Your message has been sent successfully.');
    }
}
