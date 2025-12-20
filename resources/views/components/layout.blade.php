@props([
'title' => null,
'description' => null,
'theme' => 'light',
'siteName' => config('app.name', 'Laravel'),
'tagline' => 'Software engineer  10+ years building products',
'logoHref' => '/',
'logoSrc' => '/img/apple-touch-icon.png',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ? ($siteName . ' - ' . $title) : $siteName }}</title>
    <meta name="description" content="{{ $description ?? 'Richard Leek\'s development blog' }}">
    <link rel="icon" sizes="32x32" href="/img/favicon-32x32.png" type="image/png">
    <link rel="icon" sizes="16x16" href="/img/favicon-16x16.png" type="image/png">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body data-theme="{{ $theme }}">
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="{{ $logoHref }}" aria-label="{{ $siteName }}">
                <img class="brand-logo" src="{{ $logoSrc }}" alt="" width="40" height="40" loading="eager" decoding="async">
                <div class="brand-text">
                    <div class="brand-name">{{ $siteName }}</div>
                    <div class="brand-tagline">{{ $tagline }}</div>
                </div>
            </a>

            <nav class="site-nav" aria-label="Primary">
                <a class="nav-link {{ request()->is('/') ? 'is-active' : '' }}" href="/">Home</a>
                <a class="nav-link {{ request()->is('projects*') ? 'is-active' : '' }}" href="/projects">Projects</a>
                <a class="nav-link {{ request()->is('posts*') ? 'is-active' : '' }}" href="/posts">Posts</a>
            </nav>
        </div>
    </header>

    <main class="site-main">
        <div class="container content">
            {{ $slot }}
        </div>
    </main>
</body>

</html>