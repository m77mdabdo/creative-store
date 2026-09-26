<?php

return [
    'title' => 'Notifications',
    'new_count' => '{0} No new notifications|{1} 1 new notification|[2,*] :count new notifications',
    'mark_all_read' => 'Mark all as read',
    'see_all' => 'See all notifications',
    'empty' => 'No notifications yet',
    'all' => 'All',
    'unread' => 'Unread',
    'marked_all' => 'All notifications marked as read.',
    'order_placed' => [
        'title' => 'New order',
        'message' => 'Order #:number received, total :total',
    ],
    'order_cancelled' => [
        'title' => 'Order cancelled',
        'message' => 'Order #:number was cancelled.',
        'message_refund' => 'Order #:number was cancelled and will be refunded.',
    ],
    'contact_message' => [
        'title' => 'New message from :name',
    ],
    'login' => [
        'title' => 'New sign-in',
        'message' => 'Your account was signed in from :ip',
    ],
    'registered' => [
        'title' => 'Account created',
        'message' => 'The account was created successfully.',
    ],
    'generic' => [
        'title' => 'Notification',
    ],
    'text' => [
        'order_placed' => 'Creative Store: your order :number is confirmed, total :total. Thank you for shopping with us.',
        'shipped' => 'Creative Store: your order :number has shipped with :carrier. Tracking number: :tracking',
        'delivered' => 'Creative Store: your order :number was delivered. We hope you love it!',
    ],
    'shipment_shipped' => [
        'title' => 'Order shipped',
        'message' => 'Order #:number is on its way, tracking :tracking',
    ],
    'shipment_delivered' => [
        'title' => 'Order delivered',
        'message' => 'Order #:number was delivered.',
    ],
];
