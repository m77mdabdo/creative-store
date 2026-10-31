<?php

return [
    'methods' => [
        'standard' => 'التوصيل العادي',
        'express' => 'التوصيل السريع',
        'next-day' => 'التوصيل بكرة',
        'same-day' => 'التوصيل في نفس اليوم',
        'pickup' => 'الاستلام من المتجر',
    ],

    'eta' => [
        'today' => 'اليوم',
        'tomorrow' => 'بكرة',
        'today_or_tomorrow' => 'اليوم أو بكرة',
        'range' => ':from – :to',
        'arrives' => 'يوصلك :when',
        'window_same_day' => 'في نفس اليوم',
        'window_exact' => '{1} خلال يوم|{2} خلال يومين|[3,10] خلال :count أيام|[11,*] خلال :count يومًا',
        'window_range' => 'من :min إلى :max أيام',
        'order_within' => 'اطلب خلال :time',
        'hours_minutes' => ':h س :m د',
        'minutes' => ':m دقيقة',
        'estimated' => 'موعد التوصيل المتوقع',
        'delivered_on' => 'تم التوصيل :when',
        'running_late' => 'الشحنة متأخرة شوية، ونتابعها معك',
        'pickup_ready' => 'جاهز للاستلام :when',
    ],
];
