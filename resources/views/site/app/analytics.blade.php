@if (\App\Support\Analytics\Analytics::enabled())
@php
    $gaId = config('analytics.ga4_id');
    $metaId = config('analytics.meta_pixel_id');
    $snapId = config('analytics.snap_pixel_id');
    $tiktokId = config('analytics.tiktok_pixel_id');
    $analyticsEvents = \App\Support\Analytics\Analytics::pending();
@endphp
@if ($gaId)
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', @json($gaId));
</script>
@endif
@if ($metaId)
<script>
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', @json($metaId));
    fbq('track', 'PageView');
</script>
@endif
@if ($snapId)
<script>
    (function(e,t,n){if(e.snaptr)return;var a=e.snaptr=function(){a.handleRequest?a.handleRequest.apply(a,arguments):a.queue.push(arguments)};a.queue=[];var s='script';var r=t.createElement(s);r.async=!0;r.src=n;var u=t.getElementsByTagName(s)[0];u.parentNode.insertBefore(r,u);})(window,document,'https://sc-static.net/scevent.min.js');
    snaptr('init', @json($snapId));
    snaptr('track', 'PAGE_VIEW');
</script>
@endif
@if ($tiktokId)
<script>
    !function(w,d,t){w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"];ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.load=function(e){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{};ttq._i[e]=[];ttq._i[e]._u=i;var o=d.createElement("script");o.type="text/javascript";o.async=!0;o.src=i+"?sdkid="+e+"&lib="+t;var a=d.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};ttq.load(@json($tiktokId));ttq.page();}(window,document,'ttq');
</script>
@endif
<script>
    // One call sends a GA4-style e-commerce event to every configured tag.
    window.csTrack = function (event, p) {
        p = p || {};
        var items = p.items || [];
        var ids = items.map(function (i) { return i.item_id; });
        var meta = { view_item: 'ViewContent', add_to_cart: 'AddToCart', begin_checkout: 'InitiateCheckout', purchase: 'Purchase', search: 'Search' }[event];
        var snap = { view_item: 'VIEW_CONTENT', add_to_cart: 'ADD_CART', begin_checkout: 'START_CHECKOUT', purchase: 'PURCHASE', search: 'SEARCH' }[event];
        var tiktok = { view_item: 'ViewContent', add_to_cart: 'AddToCart', begin_checkout: 'InitiateCheckout', purchase: 'CompletePayment', search: 'Search' }[event];

        if (window.gtag) gtag('event', event, p);
        if (window.fbq && meta) fbq('track', meta, { value: p.value, currency: p.currency, content_ids: ids, content_type: 'product', num_items: items.length, search_string: p.search_term }, p.transaction_id ? { eventID: p.transaction_id } : undefined);
        if (window.snaptr && snap) snaptr('track', snap, { price: p.value, currency: p.currency, item_ids: ids, number_items: items.length, transaction_id: p.transaction_id, search_string: p.search_term });
        if (window.ttq && tiktok) ttq.track(tiktok, { value: p.value, currency: p.currency, contents: items.map(function (i) { return { content_id: i.item_id, content_name: i.item_name, quantity: i.quantity, price: i.price }; }), content_type: 'product' });
        @if (config('analytics.debug'))
        console.info('[analytics]', event, p);
        @endif
    };
    @foreach ($analyticsEvents as $analyticsEvent)
    csTrack(@json($analyticsEvent['event']), @json((object) $analyticsEvent['params']));
    @endforeach
</script>
@endif
