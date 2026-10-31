<?php

return [
    'methods' => [
        'standard' => 'Standard delivery',
        'express' => 'Express delivery',
        'next-day' => 'Next-day delivery',
        'same-day' => 'Same-day delivery',
        'pickup' => 'Store pickup',
    ],

    'eta' => [
        'today' => 'Today',
        'tomorrow' => 'Tomorrow',
        'today_or_tomorrow' => 'Today or tomorrow',
        'range' => ':from – :to',
        'arrives' => 'Arrives :when',
        'window_same_day' => 'Same day',
        'window_exact' => '{1} :count day|[2,*] :count days',
        'window_range' => ':min – :max days',
        'order_within' => 'Order within :time',
        'hours_minutes' => ':h h :m min',
        'minutes' => ':m min',
        'estimated' => 'Estimated delivery',
        'delivered_on' => 'Delivered :when',
        'running_late' => 'Running a little late, we are on it',
        'pickup_ready' => 'Ready for pickup :when',
    ],
];
