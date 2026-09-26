<?php

namespace App\Support\Notifications;

use App\Notifications\LoginNotify;
use App\Notifications\NewContactMessageNotification;
use App\Notifications\OrderCancelledNotification;
use App\Notifications\OrderPlacedNotification;
use App\Notifications\RegisteredNotify;
use App\Notifications\ShipmentStatusNotification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Turns a stored database notification into what the dashboard shows:
 * a title and message in the current language, an icon, a colour and the
 * page it opens. Notifications store raw data in different shapes (and
 * older ones hard-coded Arabic or English text), so display is decided
 * here from the notification class instead.
 */
final class NotificationPresenter
{
    public static function present(DatabaseNotification $notification): object
    {
        $data = (array) $notification->data;
        $orderNumber = $data['order_number'] ?? null;
        $orderId = $data['order_id'] ?? null;

        [$icon, $tone, $title, $message, $url] = match ($notification->type) {
            OrderPlacedNotification::class => [
                'fa-shopping-bag', 'primary',
                __('notifications.order_placed.title'),
                __('notifications.order_placed.message', [
                    'number' => $orderNumber ?? '—',
                    'total'  => isset($data['total']) ? money($data['total']) : '—',
                ]),
                self::orderUrl($orderId),
            ],
            OrderCancelledNotification::class => [
                'fa-times-circle', 'danger',
                __('notifications.order_cancelled.title'),
                __(! empty($data['meta']['was_paid_online'] ?? $data['was_paid_online'] ?? false)
                    ? 'notifications.order_cancelled.message_refund'
                    : 'notifications.order_cancelled.message', ['number' => $orderNumber ?? '—']),
                self::orderUrl($orderId),
            ],
            NewContactMessageNotification::class => [
                'fa-envelope', 'info',
                __('notifications.contact_message.title', ['name' => $data['name'] ?? '—']),
                Str::limit((string) (($data['subject'] ?? '') ?: ($data['message'] ?? '')), 90),
                $data['url'] ?? self::route('dashboard.contact-messages.index'),
            ],
            ShipmentStatusNotification::class => [
                ($data['type'] ?? '') === 'shipment_delivered' ? 'fa-check-circle' : 'fa-truck',
                ($data['type'] ?? '') === 'shipment_delivered' ? 'success' : 'info',
                __('notifications.' . (($data['type'] ?? '') === 'shipment_delivered' ? 'shipment_delivered' : 'shipment_shipped') . '.title'),
                __('notifications.' . (($data['type'] ?? '') === 'shipment_delivered' ? 'shipment_delivered' : 'shipment_shipped') . '.message', [
                    'number'   => $orderNumber ?? '—',
                    'tracking' => $data['tracking_number'] ?? '—',
                ]),
                self::orderUrl($orderId),
            ],
            LoginNotify::class => [
                'fa-sign-in-alt', 'secondary',
                __('notifications.login.title'),
                __('notifications.login.message', ['ip' => $data['ip'] ?? '—']),
                null,
            ],
            RegisteredNotify::class => [
                'fa-user-plus', 'success',
                __('notifications.registered.title'),
                __('notifications.registered.message'),
                null,
            ],
            default => [
                'fa-bell', 'secondary',
                (string) ($data['title'] ?? __('notifications.generic.title')),
                (string) ($data['message'] ?? $data['text'] ?? ''),
                $data['url'] ?? null,
            ],
        };

        return (object) [
            'id'      => $notification->id,
            'icon'    => $icon,
            'tone'    => $tone,
            'title'   => $title,
            'message' => $message,
            'url'     => $url,
            'time'    => $notification->created_at?->diffForHumans(),
            'date'    => $notification->created_at?->translatedFormat('d M Y, h:i A'),
            'unread'  => $notification->read_at === null,
        ];
    }

    private static function orderUrl(mixed $orderId): ?string
    {
        return $orderId ? self::route('dashboard.orders.show', $orderId) : null;
    }

    private static function route(string $name, mixed $parameters = []): ?string
    {
        return Route::has($name) ? route($name, $parameters) : null;
    }
}
