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
        'cart_reminder' => '{1} Creative Store: an item is still waiting in your cart. Complete your order: :link|[2,*] Creative Store: :count items are still waiting in your cart. Complete your order: :link',
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
    'cart_email' => [
        'subject_first' => 'Items are still waiting in your cart',
        'subject_followup' => 'Your cart is still saved for you',
        'subject_last' => 'Last reminder: your cart is about to expire',
        'body_followup' => 'Your picks are still in your cart. Stock moves fast, so complete your order before they sell out.',
        'body_last' => 'This is our last reminder about your cart. Complete your order now so you do not miss out.',
        'title' => 'Your cart is waiting',
        'greeting' => 'Hi :name,',
        'greeting_guest' => 'Hello,',
        'body' => 'You added items to your cart but did not complete your order. They are saved for you, and you can finish in one click.',
        'product' => 'Product',
        'quantity' => 'Qty',
        'price' => 'Price',
        'button' => 'Return to your cart',
        'expiry' => 'This link is valid for 3 days. Prices and availability are confirmed at checkout.',
    ],
];
