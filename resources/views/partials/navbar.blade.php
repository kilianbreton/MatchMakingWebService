<nav class="navbar">
    <div class="navbar-left">
        <a href="/" class="nav-link">Home</a>
        <a href="/queues" class="nav-link">Queues</a>
        <a href="/statistics" class="nav-link">Statistics</a>
    </div>

    <div class="navbar-right">
        @auth
            <a href="/profile" class="nav-user">
                {{ auth()->user()->name ?? auth()->user()->login }}
            </a>
        @else
            <a href="{{ route('maniaplanet.redirect') }}" class="nav-button">
                Connection
            </a>
        @endauth
    </div>
</nav>