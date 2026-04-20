<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin')</title>

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @vite(['resources/js/app.js'])
</head>
<body class="admin-body">

    @include('partials.admin-navbar')

    <div class="admin-wrapper">
        @yield('content')
    </div>

</body>
</html>