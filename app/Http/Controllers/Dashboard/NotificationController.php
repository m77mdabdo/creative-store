<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Support\Notifications\NotificationPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
     * Live feed polled by every open dashboard page: the bell's unread count
     * and rows, anything that arrived since the page last asked (shown as a
     * pop-up), and the order and message counters in the top bar.
     */
    public function feed(Request $request): JsonResponse
    {
        $user = $request->user();
        // Timestamps are stored in the app timezone, so compare in it too.
        $since = Carbon::createFromTimestamp(max(0, (int) $request->query('since', 0)))
            ->setTimezone(config('app.timezone'));

        $latest = $user->notifications()->latest()->take(8)->get();
        $unread = $user->unreadNotifications()->count();

        $fresh = $request->filled('since')
            ? $user->notifications()
                ->where('created_at', '>=', $since)
                ->latest()
                ->take(5)
                ->get()
                ->map(fn ($n) => NotificationPresenter::present($n))
                ->map(fn ($item) => [
                    'id'      => $item->id,
                    'title'   => $item->title,
                    'message' => $item->message,
                    'icon'    => $item->icon,
                    'tone'    => $item->tone,
                    'url'     => route('dashboard.notifications.open', $item->id),
                ])
                ->values()
            : collect();

        return response()->json([
            'unread'         => $unread,
            'unread_label'   => trans_choice('notifications.new_count', $unread, ['count' => $unread]),
            'html'           => view('dashboard.notifications._dropdown-list', [
                'items' => $latest->map(fn ($n) => NotificationPresenter::present($n)),
            ])->render(),
            'fresh'          => $fresh,
            'latest'         => $latest->first()?->created_at?->getTimestamp() ?? $since->getTimestamp(),
            'pending_orders' => Order::query()->whereIn('status', ['pending', 'processing'])->count(),
            'unread_messages' => ContactMessage::query()->where('is_read', false)->count(),
        ])->header('Cache-Control', 'no-store');
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
