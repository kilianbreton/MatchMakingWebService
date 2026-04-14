<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Shootmania Matchmaking')</title>
    <link rel="stylesheet" href="{{ asset('css/matches.css') }}">
</head>
<body>

    @include('partials.navbar')

    <div class="site-wrapper">
        <div class="site-wrapper">
            @if(session('success'))
                <div class="flash-success">{{ session('success') }}</div>
            @endif
        
            @if(session('error'))
                <div class="profile-error">{{ session('error') }}</div>
            @endif
        
            @yield('content')
        </div>
       
    </div>
</body>
</html>