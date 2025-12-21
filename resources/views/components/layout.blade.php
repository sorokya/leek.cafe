@props([
'title' => null,
'description' => null,
'theme' => null,
'siteName' => 'Richard Leek',
'tagline' => 'Software engineer — 10+ years building products',
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

<body @if ($theme !=='system' ) data-theme="{{ $theme }}" @endif>
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

                <form class="theme-toggle" method="POST" action="{{ route('theme.toggle') }}">
                    @csrf
                    <button class="theme-toggle-button" type="submit" aria-label="Toggle theme" title="Toggle theme">
                        <svg class="theme-icon theme-icon--dark" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false">
                            <path fill="currentColor" d="M12 18a6 6 0 0 1 0-12a.75.75 0 0 0 .65-1.13A8 8 0 1 0 19.13 11.35A.75.75 0 0 0 18 12a6 6 0 0 1-6 6Z" />
                        </svg>
                        <svg class="theme-icon theme-icon--light" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false">
                            <path fill="currentColor" d="M12 18a6 6 0 1 1 0-12a6 6 0 0 1 0 12Zm0-14.5a.75.75 0 0 1 .75.75v1a.75.75 0 0 1-1.5 0v-1A.75.75 0 0 1 12 3.5Zm0 15a.75.75 0 0 1 .75.75v1a.75.75 0 0 1-1.5 0v-1A.75.75 0 0 1 12 18.5ZM4.72 6.22a.75.75 0 0 1 1.06 0l.71.71a.75.75 0 1 1-1.06 1.06l-.71-.71a.75.75 0 0 1 0-1.06Zm12.02 12.02a.75.75 0 0 1 1.06 0l.71.71a.75.75 0 0 1-1.06 1.06l-.71-.71a.75.75 0 0 1 0-1.06ZM3.5 12a.75.75 0 0 1 .75-.75h1a.75.75 0 0 1 0 1.5h-1A.75.75 0 0 1 3.5 12Zm15 0a.75.75 0 0 1 .75-.75h1a.75.75 0 0 1 0 1.5h-1A.75.75 0 0 1 18.5 12ZM6.22 19.28a.75.75 0 0 1 0-1.06l.71-.71a.75.75 0 0 1 1.06 1.06l-.71.71a.75.75 0 0 1-1.06 0ZM18.24 7.26a.75.75 0 0 1 0-1.06l.71-.71a.75.75 0 0 1 1.06 1.06l-.71.71a.75.75 0 0 1-1.06 0Z" />
                        </svg>
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
</body>

</html>