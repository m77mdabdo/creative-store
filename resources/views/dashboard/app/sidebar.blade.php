<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ $sidebar['brand']['route'] ?? '#' }}" class="brand-link text-center">
        <img
            src="{{ $sidebar['brand']['logo'] ?? asset('AdminLTE/dist/img/AdminLTELogo.png') }}"
            alt="Logo"
            class="brand-image img-circle elevation-3"
            style="opacity: .8"
        >
        <span class="brand-text font-weight-light">
            {{ $sidebar['brand']['title'] ?? config('app.name') }}
        </span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image">
                <img
                    src="{{ $sidebar['user']['avatar'] ?? asset('AdminLTE/dist/img/user1-128x128.jpg') }}"
                    class="img-circle elevation-2"
                    alt="User Image"
                >
            </div>
            <div class="info">
                <a href="#" class="d-block">
                    {{ $sidebar['user']['name'] ?? 'Dashboard User' }}
                </a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent"
                data-widget="treeview"
                role="menu"
                data-accordion="false">

                @foreach(($sidebar['items'] ?? []) as $item)
                    @if(($item['type'] ?? null) === 'single')
                        <li class="nav-item">
                            <a href="{{ $item['url'] }}" class="nav-link {{ !empty($item['active']) ? 'active' : '' }}">
                                <i class="nav-icon {{ $item['icon'] }}"></i>
                                <p>
                                    {{ $item['title'] }}
                                    @if(!empty($item['badge']))
                                        <span class="right {{ $item['badge']['class'] }}">
                                            {{ $item['badge']['value'] }}
                                        </span>
                                    @endif
                                </p>
                            </a>
                        </li>
                    @elseif(($item['type'] ?? null) === 'tree')
                        <li class="nav-item {{ !empty($item['open']) ? 'menu-open' : '' }}">
                            <a href="{{ $item['url'] ?? '#' }}" class="nav-link {{ !empty($item['active']) ? 'active' : '' }}">
                                <i class="nav-icon {{ $item['icon'] }}"></i>
                                <p>
                                    {{ $item['title'] }}
                                    @if(!empty($item['badge']))
                                        <span class="right {{ $item['badge']['class'] }}">
                                            {{ $item['badge']['value'] }}
                                        </span>
                                    @endif
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">
                                @foreach(($item['children'] ?? []) as $child)
                                    <li class="nav-item">
                                        <a href="{{ $child['url'] }}" class="nav-link {{ !empty($child['active']) ? 'active' : '' }}">
                                            <i class="{{ $child['icon'] ?? 'far fa-circle' }} nav-icon"></i>
                                            <p>
                                                {{ $child['title'] }}
                                                @if(!empty($child['badge']))
                                                    <span class="right {{ $child['badge']['class'] }}">
                                                        {{ $child['badge']['value'] }}
                                                    </span>
                                                @endif
                                            </p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                @endforeach

                <li class="nav-item mt-3 px-2">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-sign-out-alt mr-1"></i>
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>
