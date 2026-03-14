<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('dashboard.home') }}" class="nav-link">Dashboard</a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ url('/') }}" class="nav-link" target="_blank">Visit Website</a>
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
                        <input class="form-control form-control-navbar"
                               type="search"
                               placeholder="Search"
                               aria-label="Search">
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
                @if(($dashboardPendingOrdersCount ?? 0) > 0)
                    <span class="badge badge-warning navbar-badge">
                        {{ $dashboardPendingOrdersCount }}
                    </span>
                @endif
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">
                    {{ $dashboardPendingOrdersCount ?? 0 }} Pending Orders
                </span>

                <div class="dropdown-divider"></div>

                @forelse(($dashboardPendingOrders ?? []) as $order)
                    <a href="{{ route('dashboard.orders.show', $order->id) }}" class="dropdown-item">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Order #{{ $order->order_number }}
                        <span class="float-right text-muted text-sm">
                            {{ $order->created_at?->diffForHumans() }}
                        </span>
                        <br>
                        <small class="text-muted">
                            {{ $order->user->name ?? $order->guest_email ?? 'Customer' }}
                        </small>
                    </a>
                    <div class="dropdown-divider"></div>
                @empty
                    <span class="dropdown-item text-muted">No pending orders</span>
                    <div class="dropdown-divider"></div>
                @endforelse

                <a href="{{ route('dashboard.orders.index') }}" class="dropdown-item dropdown-footer">
                    See All Orders
                </a>
            </div>
        </li>

        {{-- Contact Messages Notifications --}}
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-envelope"></i>
                @if(($dashboardUnreadMessagesCount ?? 0) > 0)
                    <span class="badge badge-danger navbar-badge">
                        {{ $dashboardUnreadMessagesCount }}
                    </span>
                @endif
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">
                    {{ $dashboardUnreadMessagesCount ?? 0 }} New Messages
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
                    <span class="dropdown-item text-muted">No new messages</span>
                    <div class="dropdown-divider"></div>
                @endforelse

                <a href="{{ route('dashboard.contact-messages.index') }}" class="dropdown-item dropdown-footer">
                    See All Messages
                </a>
            </div>
        </li>

        {{-- Fullscreen --}}
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

        {{-- User Dropdown --}}
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user-circle"></i>
                <span class="ml-1">{{ auth()->user()->name ?? 'User' }}</span>
            </a>

            <div class="dropdown-menu dropdown-menu-right">
                <span class="dropdown-item-text">
                    <strong>{{ auth()->user()->name ?? 'User' }}</strong><br>
                    <small>{{ auth()->user()->email ?? '' }}</small>
                </span>

                <div class="dropdown-divider"></div>

                <a href="{{ route('myaccount') }}" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> My Account
                </a>

                <div class="dropdown-divider"></div>

                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>
