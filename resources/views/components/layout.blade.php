@inject('preferences', 'App\\Services\\UserPreferences')

@php
    $preferences->parse_from_request(request());
    $theme = $preferences->theme->value;

    $pageTitle = $title ? $siteName . ' - ' . $title : $siteName;
    $pageDescription = $description ?? "Richard Leek's development blog";
    $pageUrl = $url ?? request()->fullUrl();

    $defaultImage = $logoSrc
        ? (str_starts_with($logoSrc, 'http://') || str_starts_with($logoSrc, 'https://')
            ? $logoSrc
            : url($logoSrc))
        : null;
    $pageImage = $image ?: $defaultImage;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">

    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:url" content="{{ $pageUrl }}">
    @if ($pageImage)
        <meta property="og:image" content="{{ $pageImage }}">
    @endif
    <link rel="icon" sizes="32x32" href="/img/favicon-32x32.png" type="image/png">
    <link rel="icon" sizes="16x16" href="/img/favicon-16x16.png" type="image/png">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @php
            $viteAssets = ['resources/css/app.css', 'resources/js/app.js'];
            if (auth()->check()) {
                $viteAssets[] = 'resources/js/thoughts.js';
            }
        @endphp

        @vite($viteAssets)
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

            <button class="nav-toggle" type="button" data-nav-toggle aria-controls="primary-nav-mobile"
                aria-expanded="false" aria-label="Toggle menu" title="Toggle menu">
                <span class="nav-toggle-icon" aria-hidden="true"><span></span></span>
            </button>

            <nav class="site-nav site-nav--desktop" aria-label="Primary">
                <x-primary-nav-items />
            </nav>

            <nav class="site-nav site-nav--mobile" id="primary-nav-mobile" aria-label="Primary" hidden>
                <x-primary-nav-items mobile="true" />
            </nav>

            <nav class="site-utils" aria-label="Site Utilities">

                <a href="/feed.xml" class="rss-icon" aria-label="RSS Feed" title="RSS Feed">
                    <x-heroicon-s-rss aria-hidden="true" focusable="false" width="18" height="18" />
                </a>

                <form class="theme-toggle" method="POST" action="{{ route('theme.toggle') }}">
                    @csrf
                    <button class="theme-toggle-button" type="submit" aria-label="Toggle theme" title="Toggle theme">
                        <x-heroicon-o-moon class="theme-icon theme-icon--dark" width="18" height="18"
                            aria-hidden="true" focusable="false" />
                        <x-heroicon-o-sun class="theme-icon theme-icon--light" width="18" height="18"
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
                &copy; {{ date('Y') }} Richard Leek. All rights reserved.
            </small>

            @if (defined('LARAVEL_START'))
                @php
                    $renderedInMs = (int) round((microtime(true) - LARAVEL_START) * 1000);
                @endphp
                <small class="footer-metric">Rendered in {{ $renderedInMs }}ms</small>
            @endif
        </div>
    </footer>
    @stack('scripts')
</body>

</html>
