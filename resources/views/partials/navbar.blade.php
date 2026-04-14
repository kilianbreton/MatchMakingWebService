<nav class="navbar">
    <div class="navbar-left">
        <a href="/" class="nav-link">Home</a>
        <a href="/queues" class="nav-link">Queues</a>
        <a href="/statistics" class="nav-link">Statistics</a>
    </div>

    <div class="navbar-right">
        @auth
        <div class="nav-dropdown" id="userDropdown">
            @php
                $nickname = auth()->user()->name ?? auth()->user()->login;
                $nickname = App\Services\TmNick::toHtml($nickname);
            @endphp
            <a class="nav-button nav-user-btn" id="dropdownToggle">
                {!! $nickname !!}
            </a>
        
            <div class="dropdown-menu" id="dropdownMenu">
                <a href="/profile">Profile</a>
        
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('dropdownToggle');
        const menu = document.getElementById('dropdownMenu');
    
        if (!toggle || !menu) return;
    
        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            menu.classList.toggle('show');
        });
    
        // Ferme si clic ailleurs
        document.addEventListener('click', function () {
            menu.classList.remove('show');
        });
    
        // Empêche fermeture si clic dans le menu
        menu.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    });
    </script>