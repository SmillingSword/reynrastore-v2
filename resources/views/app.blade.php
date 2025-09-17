<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Reynra Store') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon.png">
    
    <!-- Meta Tags for SEO -->
    <meta name="description" content="Reynra Store - Platform top up game terpercaya dengan harga terjangkau dan proses otomatis. Dapatkan diamond, UC, dan item game favoritmu dengan mudah dan aman.">
    <meta name="keywords" content="top up game, mobile legends, free fire, pubg mobile, genshin impact, valorant, steam wallet, diamond, UC, genesis crystal">
    <meta name="author" content="Reynra Store">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Reynra Store - Game Top Up Terpercaya">
    <meta property="og:description" content="Platform top up game terpercaya dengan harga terjangkau dan proses otomatis">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ asset('images/logo/logo-1024.png') }}">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Reynra Store - Game Top Up Terpercaya">
    <meta name="twitter:description" content="Platform top up game terpercaya dengan harga terjangkau dan proses otomatis">
    <meta name="twitter:image" content="{{ asset('images/logo/logo-1024.png') }}">
    
    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Additional head content -->
    @stack('head')
</head>
<body class="antialiased">
    <!-- Vue.js App Mount Point -->
    <div id="app"></div>
    
    <!-- Additional scripts -->
    @stack('scripts')
    
    <!-- Development tools (only in development) -->
    @if(app()->environment('local'))
        <script>
            // Vue DevTools detection
            if (typeof window !== 'undefined' && window.__VUE_DEVTOOLS_GLOBAL_HOOK__) {
                console.log('Vue DevTools detected');
            }
        </script>
    @endif
</body>
</html>
