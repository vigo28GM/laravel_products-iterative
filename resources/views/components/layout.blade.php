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
        <a href="{{ route('products.index') }}">Products</a>
    </header>
    <nav>
        <a href="{{ route('products.index') }}">All products</a>
        <a href="{{ route('products.create') }}">New product</a>
    </nav>
    <main>
        {{ $slot }}
    </main>
    <aside>Sidebar</aside>
    <footer>&copy; {{ date('Y') }} Products</footer>
</body>
</html>
