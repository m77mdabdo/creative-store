<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('dashboard.home') }}" class="nav-link">{{ __('site.Dashboard') }}</a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ url('/') }}" class="nav-link" target="_blank">{{ __('site.Visit Website') }}</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        {{-- Search --}}
        <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <i class="fas fa-search"></i>
            </a>

            <div class="navbar-search-block">
                <form class="form-inline" method="GET" action="#">
                    <div class="input-group input-group-sm">
                        <input
                            class="form-control form-control-navbar"
                            type="search"
                            placeholder="{{ __('site.Search') }}"
                            aria-label="{{ __('site.Search') }}"
                        >
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        {{-- Orders Notifications --}}
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-shopping-bag"></i>
                <span class="badge badge-warning navbar-badge" id="nav-orders-count" @if(($dashboardPendingOrdersCount ?? 0) === 0) hidden @endif>{{ $dashboardPendingOrdersCount ?? 0 }}</span>
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">
                    {{ trans('site.:count Pending Orders', ['count' => $dashboardPendingOrdersCount ?? 0]) }}
                </span>

                <div class="dropdown-divider"></div>

                @forelse(($dashboardPendingOrders ?? []) as $order)
                    <a href="{{ route('dashboard.orders.show', $order->id) }}" class="dropdown-item">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        {{ __('site.Order') }} #{{ $order->order_number }}
                        <span class="float-right text-muted text-sm">
                            {{ $order->created_at?->diffForHumans() }}
                        </span>
                        <br>
                        <small class="text-muted">
                            {{ $order->user->name ?? $order->guest_email ?? __('site.Customer') }}
                        </small>
                    </a>
                    <div class="dropdown-divider"></div>
                @empty
                    <span class="dropdown-item text-muted">{{ __('site.No pending orders') }}</span>
                    <div class="dropdown-divider"></div>
                @endforelse

                <a href="{{ route('dashboard.orders.index') }}" class="dropdown-item dropdown-footer">
                    {{ __('site.See All Orders') }}
                </a>
            </div>
        </li>

        {{-- Contact Messages Notifications --}}
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-envelope"></i>
                <span class="badge badge-danger navbar-badge" id="nav-messages-count" @if(($dashboardUnreadMessagesCount ?? 0) === 0) hidden @endif>{{ $dashboardUnreadMessagesCount ?? 0 }}</span>
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">
                    {{ trans('site.:count New Messages', ['count' => $dashboardUnreadMessagesCount ?? 0]) }}
                </span>

                <div class="dropdown-divider"></div>

                @forelse(($dashboardUnreadMessages ?? []) as $message)
                    <a href="{{ route('dashboard.contact-messages.index') }}" class="dropdown-item">
                        <div>
                            <strong>{{ $message->name }}</strong>
                            <span class="float-right text-muted text-sm">
                                {{ $message->created_at?->diffForHumans() }}
                            </span>
                        </div>
                        <div class="text-sm text-muted">
                            {{ \Illuminate\Support\Str::limit($message->subject ?: $message->message, 40) }}
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>
                @empty
                    <span class="dropdown-item text-muted">{{ __('site.No new messages') }}</span>
                    <div class="dropdown-divider"></div>
                @endforelse

                <a href="{{ route('dashboard.contact-messages.index') }}" class="dropdown-item dropdown-footer">
                    {{ __('site.See All Messages') }}
                </a>
            </div>
        </li>

        {{-- Reviews Notifications --}}
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-comments"></i>
                @if(($dashboardPendingReviewsCount ?? 0) > 0)
                    <span class="badge badge-warning navbar-badge">
                        {{ $dashboardPendingReviewsCount }}
                    </span>
                @endif
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">
                    {{ trans('site.:count Pending Reviews', ['count' => $dashboardPendingReviewsCount ?? 0]) }}
                </span>

                <div class="dropdown-divider"></div>

                @forelse(($dashboardPendingReviews ?? []) as $review)
                    <a href="{{ route('dashboard.reviews.show', $review->id) }}" class="dropdown-item">
                        <div>
                            <strong>{{ $review->user->name ?? __('site.Customer') }}</strong>
                            <span class="float-right text-muted text-sm">
                                {{ $review->created_at?->diffForHumans() }}
                            </span>
                        </div>
                        <div class="text-sm text-muted">
                            {{ $review->product->name ?? $review->product->sku ?? __('site.Product') }} — {{ $review->rating }}/5
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>
                @empty
                    <span class="dropdown-item text-muted">{{ __('site.No pending reviews') }}</span>
                    <div class="dropdown-divider"></div>
                @endforelse

                <a href="{{ route('dashboard.reviews.index', ['status' => 'pending']) }}" class="dropdown-item dropdown-footer">
                    {{ __('site.See Pending Reviews') }}
                </a>
            </div>
        </li>

        {{-- User Notifications --}}
        @auth
            @php
                $notifUnread = auth()->user()->unreadNotifications()->count();
                $notifItems = auth()->user()->notifications()->latest()->take(8)->get()
                    ->map(fn ($n) => \App\Support\Notifications\NotificationPresenter::present($n));
            @endphp
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#" role="button"
                   aria-label="{{ __('notifications.title') }}" aria-haspopup="true" aria-expanded="false">
                    <i class="far fa-bell" id="notif-bell-icon"></i>
                    <span id="notif-count" class="badge badge-danger navbar-badge" @if ($notifUnread === 0) hidden @endif>{{ $notifUnread > 99 ? '99+' : $notifUnread }}</span>
                </a>

                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right cs-notif-menu" id="notif-dropdown">
                    <div class="cs-notif-head">
                        <strong>{{ __('notifications.title') }}</strong>
                        <span class="text-muted" id="notif-new-count">{{ trans_choice('notifications.new_count', $notifUnread, ['count' => $notifUnread]) }}</span>
                    </div>

                    <div class="cs-notif-list" id="notif-list">
                        @include('dashboard.notifications._dropdown-list', ['items' => $notifItems])
                    </div>

                    <div class="cs-notif-foot">
                        <form method="POST" action="{{ route('dashboard.notifications.read-all') }}" id="notif-read-all" @if ($notifUnread === 0) hidden @endif>
                            @csrf
                            <button type="submit" class="btn btn-link btn-sm p-0">{{ __('notifications.mark_all_read') }}</button>
                        </form>
                        <a href="{{ route('dashboard.notifications.index') }}">{{ __('notifications.see_all') }}</a>
                    </div>
                </div>
            </li>
        @endauth

        {{-- Fullscreen --}}
        <li class="nav-item d-none d-md-block">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

        {{-- Language Switcher --}}
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-globe"></i>
                <span class="ml-1">{{ app()->getLocale() === 'ar' ? 'ع' : 'EN' }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ LaravelLocalization::getLocalizedURL('ar') }}"
                   class="dropdown-item {{ app()->getLocale() === 'ar' ? 'active' : '' }}">
                    🇸🇦 العربية
                </a>
                <a href="{{ LaravelLocalization::getLocalizedURL('en') }}"
                   class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}">
                    {{ __('🇬🇧 English') }}
                </a>
            </div>
        </li>

        {{-- User Dropdown --}}
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                @if (auth()->user()?->avatarUrl())
                    <img src="{{ auth()->user()->avatarUrl() }}" alt="" width="24" height="24"
                         class="img-circle" style="width:24px;height:24px;object-fit:cover;margin-top:-3px;">
                @else
                    <i class="fas fa-user-circle"></i>
                @endif
                <span class="ml-1 d-none d-md-inline">{{ auth()->user()->name ?? __('site.Guest') }}</span>
            </a>

            <div class="dropdown-menu dropdown-menu-right">
                <span class="dropdown-item-text">
                    <strong>{{ auth()->user()->name ?? __('site.Guest') }}</strong><br>
                    <small>{{ auth()->user()->email ?? '' }}</small>
                </span>

                <div class="dropdown-divider"></div>

                <a href="{{ route('myaccount') }}" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> {{ __('account.profile') }}
                </a>

                <div class="dropdown-divider"></div>

                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="fas fa-sign-out-alt mr-2"></i> {{ __('site.Logout') }}
                    </button>
                </form>
            </div>
        </li>

    </ul>
</nav>
