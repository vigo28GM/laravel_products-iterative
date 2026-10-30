<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Products' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header>
        <a href="{{ route('products.index') }}"><img src="{{ asset('images/logo.svg') }}" alt="Logo" class="logo"></a>
    </header>
    <nav>
        <ul>
            <li><a href="{{ route('products.index') }}">All products</a></li>
            <li><a href="{{ route('products.create') }}">New product</a></li>
        </ul>
    </nav>
    <main>
        {{ $slot }}
    </main>
    <aside>Advertisement test: Buy now, 50% off! While being a CCP member</aside>
    <footer>&copy; {{ date('Y') }} All rights reserved to the USSR. The USSR will hunt you down</footer>
</body>
</html>
