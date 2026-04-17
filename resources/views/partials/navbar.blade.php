<nav class="navbar">
    <div class="navbar-left">
        <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">Home</a>
        <a href="{{ route('queues.index') }}" class="nav-link {{ request()->routeIs('queues.*') ? 'active' : '' }}">Queues</a>

        <div class="nav-dropdown">
            <button class="nav-link dropdown-toggle">
                Statistics
            </button>

            <div class="dropdown-menu">
                @foreach($navbarGamemodes as $mode)
                    <a href="{{ route('statistics.show', $mode) }}">
                        {{ $mode->name }}
                    </a>
                @endforeach
            </div>
        </div>
        @can('access admin')
            <a class="nav-link" href="{{ route('admin.index') }}">Administration</a>
        @endcan
    </div>

    <div class="navbar-right">
        @auth
            <div class="nav-dropdown">
                <button class="nav-button nav-user-btn dropdown-toggle">
                    {!! App\Services\TmNick::toHtml(auth()->user()->name ?? auth()->user()->login) !!}
                </button>

                <div class="dropdown-menu">
                    <a href="{{ route('profile') }}">Profile</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ route('maniaplanet.redirect') }}" class="nav-button">
                Connection
            </a>
        @endauth
    </div>
</nav>