<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Support\Notifications\NotificationPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $onlyUnread = $request->query('filter') === 'unread';

        $query = $onlyUnread
            ? $request->user()->unreadNotifications()
            : $request->user()->notifications();

        $notifications = $query->latest()->paginate(20)->withQueryString();

        return view('dashboard.notifications.index', [
            'notifications' => $notifications,
            'items'         => $notifications->getCollection()->map(fn ($n) => NotificationPresenter::present($n)),
            'onlyUnread'    => $onlyUnread,
            'unreadCount'   => $request->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark one notification as read and open the page it refers to.
     */
    public function open(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->whereKey($id)->firstOrFail();

        $notification->markAsRead();

        $url = NotificationPresenter::present($notification)->url;

        return $url ? redirect()->to($url) : redirect()->route('dashboard.notifications.index');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', __('notifications.marked_all'));
    }
}
