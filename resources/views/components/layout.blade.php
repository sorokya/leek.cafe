<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ? (config('app.name', 'Laravel') . ' - ' . $title) : config('app.name', 'Laravel') }}</title>
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
    {{ $slot }}
</body>

</html>