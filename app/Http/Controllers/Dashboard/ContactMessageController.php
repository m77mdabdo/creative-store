<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'q'        => trim((string) $request->input('q', '')),
            'status'   => (string) $request->input('status', ''),
            'sort'     => (string) $request->input('sort', 'latest'),
            'per_page' => (int) $request->input('per_page', 15),
        ];

        if (! in_array($filters['sort'], ['latest', 'oldest', 'name_asc', 'name_desc'], true)) {
            $filters['sort'] = 'latest';
        }

        if (! in_array($filters['per_page'], [10, 15, 25, 50, 100], true)) {
            $filters['per_page'] = 15;
        }

        $query = ContactMessage::query();

        if ($filters['q'] !== '') {
            $query->where(function ($q) use ($filters) {
                $term = $filters['q'];

                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('subject', 'like', "%{$term}%")
                    ->orWhere('message', 'like', "%{$term}%");
            });
        }

        if ($filters['status'] === 'read') {
            $query->where('is_read', true);
        } elseif ($filters['status'] === 'unread') {
            $query->where('is_read', false);
        }

        switch ($filters['sort']) {
            case 'oldest':
                $query->oldest('id');
                break;

            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;

            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;

            case 'latest':
            default:
                $query->latest('id');
                break;
        }

        $messages = $query
            ->paginate($filters['per_page'])
            ->withQueryString();

        $stats = [
            'total' => ContactMessage::query()->count(),
            'unread' => ContactMessage::query()->where('is_read', false)->count(),
            'read' => ContactMessage::query()->where('is_read', true)->count(),
        ];

        return view('dashboard.contact-messages.index', compact('messages', 'filters', 'stats'));
    }

    public function show(ContactMessage $message): View
    {
        if (! $message->is_read) {
            $message->update([
                'is_read' => true,
            ]);
        }

        return view('dashboard.contact-messages.show', compact('message'));
    }

    public function markAsRead(ContactMessage $message): RedirectResponse
    {
        $message->update([
            'is_read' => true,
        ]);

        return back()->with('success', __('Message marked as read successfully.'));
    }

    public function markAsUnread(ContactMessage $message): RedirectResponse
    {
        $message->update([
            'is_read' => false,
        ]);

        return back()->with('success', __('Message marked as unread successfully.'));
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:read,unread,delete'],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:contact_messages,id'],
        ]);

        $query = ContactMessage::query()->whereIn('id', $data['ids']);

        if ($data['action'] === 'read') {
            $query->update(['is_read' => true]);

            return back()->with('success', __('Selected messages marked as read.'));
        }

        if ($data['action'] === 'unread') {
            $query->update(['is_read' => false]);

            return back()->with('success', __('Selected messages marked as unread.'));
        }

        if ($data['action'] === 'delete') {
            $query->delete();

            return back()->with('success', __('Selected messages deleted successfully.'));
        }

        return back()->with('error', __('Invalid action.'));
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return redirect()
            ->route('dashboard.contact-messages.index')
            ->with('success', __('Message deleted successfully.'));
    }
}
