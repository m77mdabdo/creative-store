<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ $sidebar['brand']['route'] ?? '#' }}" class="brand-link">
        <img
            src="{{ $sidebar['brand']['logo'] ?? asset('AdminLTE/dist/img/AdminLTELogo.png') }}"
            alt="{{ $sidebar['brand']['title'] ?? 'Dashboard' }} Logo"
            class="brand-image img-circle elevation-3"
            style="opacity:.8"
        >
        <span class="brand-text font-weight-light">
            {{ $sidebar['brand']['title'] ?? 'Dashboard' }}
        </span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img
                    src="{{ $sidebar['user']['avatar'] ?? asset('AdminLTE/dist/img/user2-160x160.jpg') }}"
                    class="img-circle elevation-2"
                    alt="User Image"
                >
            </div>
            <div class="info">
                <a href="javascript:void(0)" class="d-block">
                    {{ $sidebar['user']['name'] ?? 'Dashboard User' }}
                </a>
            </div>
        </div>

        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input
                    class="form-control form-control-sidebar"
                    type="search"
                    placeholder="Search"
                    aria-label="Search"
                >
                <div class="input-group-append">
                    <button class="btn btn-sidebar" type="button">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <nav class="mt-2">
            <ul
                class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview"
                role="menu"
                data-accordion="false"
            >
                @foreach(($sidebar['items'] ?? []) as $item)

                    @if(($item['type'] ?? null) === 'single')
                        <li class="nav-item">
                            <a href="{{ $item['url'] ?? '#' }}"
                               class="nav-link {{ !empty($item['active']) ? 'active' : '' }}">
                                <i class="nav-icon {{ $item['icon'] ?? 'far fa-circle' }}"></i>
                                <p>
                                    {{ $item['title'] ?? 'Item' }}

                                    @if(!empty($item['badge']))
                                        <span class="right {{ $item['badge']['class'] }}">
                                            {{ $item['badge']['value'] }}
                                        </span>
                                    @endif
                                </p>
                            </a>
                        </li>
                    @endif

                    @if(($item['type'] ?? null) === 'tree')
                        <li class="nav-item has-treeview {{ !empty($item['open']) ? 'menu-open' : '' }}">
                            <a href="{{ $item['url'] ?? '#' }}"
                               class="nav-link {{ !empty($item['active']) ? 'active' : '' }}">
                                <i class="nav-icon {{ $item['icon'] ?? 'far fa-circle' }}"></i>
                                <p>
                                    {{ $item['title'] ?? 'Group' }}

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
                                        <a href="{{ $child['url'] ?? '#' }}"
                                           class="nav-link {{ !empty($child['active']) ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>{{ $child['title'] ?? 'Child' }}</p>
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
