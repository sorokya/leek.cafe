@props([
    'title' => null,
    'description' => null,
    'theme' => null,
    'siteName' => 'Richard Leek',
    'tagline' => 'Software engineer — 10+ years building cool projects',
    'logoHref' => '/',
    'logoSrc' => '/img/apple-touch-icon.png',
])

@inject('preferences', 'App\\Services\\UserPreferences')

@php
    $preferences->parse_from_request(request());
    $theme = $preferences->theme->value;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ? $siteName . ' - ' . $title : $siteName }}</title>
    <meta name="description" content="{{ $description ?? 'Richard Leek\'s development blog' }}">
    <link rel="icon" sizes="32x32" href="/img/favicon-32x32.png" type="image/png">
    <link rel="icon" sizes="16x16" href="/img/favicon-16x16.png" type="image/png">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body @if ($theme !== 'system') data-theme="{{ $theme }}" @endif>
    <header class="site-header">
        <div class="container header-inner">
            <div class="brand-container">
                <a class="brand" href="{{ $logoHref }}" aria-label="{{ $siteName }}">
                    <img class="brand-logo" src="{{ $logoSrc }}" alt="" width="40" height="40"
                        loading="eager" decoding="async">
                </a>
                <div class="brand-text">
                    <div class="brand-name">{{ $siteName }}</div>
                    <div class="brand-tagline">{{ $tagline }}</div>
                </div>
            </div>

            <nav class="site-nav" aria-label="Primary">
                <a class="nav-link {{ request()->is('/') ? 'is-active' : '' }}" href="/">Home</a>
                <a class="nav-link {{ request()->is('projects*') ? 'is-active' : '' }}" href="/projects">Projects</a>
                <a class="nav-link {{ request()->is('posts*') ? 'is-active' : '' }}" href="/posts">Posts</a>

                @auth
                    <a class="nav-link" href="/settings">Settings</a>

                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button class="nav-link" type="submit">Logout</button>
                    </form>
                @endauth

                <form class="theme-toggle" method="POST" action="{{ route('theme.toggle') }}">
                    @csrf
                    <button class="theme-toggle-button" type="submit" aria-label="Toggle theme" title="Toggle theme">
                        <x-iconic-moon class="theme-icon theme-icon--dark" width="18" height="18"
                            aria-hidden="true" focusable="false" />
                        <x-iconic-sun class="theme-icon theme-icon--light" width="18" height="18"
                            aria-hidden="true" focusable="false" />
                    </button>
                </form>
            </nav>
        </div>
    </header>

    <main class="site-main">
        <div class="container content">
            {{ $slot }}
        </div>
    </main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <small class="footer-text">
                &copy; {{ date('Y') }} {{ $siteName }}
            </small>

            @if (defined('LARAVEL_START'))
                @php
                    $renderedInMs = (int) round((microtime(true) - LARAVEL_START) * 1000);
                @endphp
                <small class="footer-metric">Rendered in {{ $renderedInMs }}ms</small>
            @endif
        </div>
    </footer>
</body>

</html>
