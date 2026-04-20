<nav class="admin-navbar">
    <div class="admin-navbar-left">
        <a href="{{ route('admin.dashboard') }}" class="admin-logo">
            Admin
        </a>

        <a href="{{ route('admin.matches') }}" class="admin-link {{ request()->routeIs('admin.matches') ? 'active' : '' }}">
            Matches
        </a>

        <a href="{{ route('admin.queues') }}" class="admin-link {{ request()->routeIs('admin.queues') ? 'active' : '' }}">
            Queues
        </a>

        <a href="{{ route('admin.players') }}" class="admin-link {{ request()->routeIs('admin.players') ? 'active' : '' }}">
            Players
        </a>

        <a href="{{ route('admin.servers') }}" class="admin-link {{ request()->routeIs('admin.servers') ? 'active' : '' }}">
            Servers
        </a>
    </div>

    <div class="admin-navbar-right">
        <a href="/" class="admin-link">Back to site</a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="admin-button">Logout</button>
        </form>
    </div>
</nav>