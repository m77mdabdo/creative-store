<section class="home-newsletter">
    <div class="container">
        <div class="home-newsletter-box">
            <div>
                <h2>{{ __('site.newsletter_title') }}</h2>
                <p>{{ __('site.newsletter_text') }}</p>
            </div>
            <form action="{{ route('newsletter.store') }}" method="POST" class="home-newsletter-form">
                @csrf
                <label for="home-newsletter-email" class="cs-visually-hidden">{{ __('site.email') }}</label>
                <input id="home-newsletter-email" type="email" name="email" required autocomplete="email"
                       placeholder="{{ __('site.email_placeholder') }}">
                <button type="submit">{{ __('site.subscribe') }}</button>
            </form>
        </div>
    </div>
</section>
