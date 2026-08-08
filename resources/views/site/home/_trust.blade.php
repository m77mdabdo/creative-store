<section class="home-trust" aria-label="{{ __('site.why_shop_with_us') }}">
    <div class="container">
        <ul class="home-trust-list">
            <li>
                <i class="zmdi zmdi-truck" aria-hidden="true"></i>
                <div>
                    <strong>{{ __('site.trust_shipping_title') }}</strong>
                    <span>{{ __('site.free_shipping_over', ['amount' => number_format((int) config('store.free_shipping_threshold', 300))]) }}</span>
                </div>
            </li>
            <li>
                <i class="zmdi zmdi-shield-check" aria-hidden="true"></i>
                <div>
                    <strong>{{ __('site.trust_payment_title') }}</strong>
                    <span>{{ __('site.trust_payment_text') }}</span>
                </div>
            </li>
            <li>
                <i class="zmdi zmdi-refresh-sync" aria-hidden="true"></i>
                <div>
                    <strong>{{ __('site.trust_returns_title') }}</strong>
                    <span>{{ __('site.trust_returns_text') }}</span>
                </div>
            </li>
            <li>
                <i class="zmdi zmdi-headset-mic" aria-hidden="true"></i>
                <div>
                    <strong>{{ __('site.trust_support_title') }}</strong>
                    <span>{{ __('site.trust_support_text') }}</span>
                </div>
            </li>
        </ul>
    </div>
</section>
