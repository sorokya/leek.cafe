<?php

declare(strict_types=1);

namespace App\Utils;

class LayoutHelper
{
    /**
     * Layout data storage
     * @var array{title: string, description: string, layout: string, content: string, stylesheets: string[], scripts: string[], has_music: bool}
     */
    private static array $__layout_data = [
        'title' => '',
        'description' => '',
        'layout' => 'base',
        'content' => '',
        'stylesheets' => ['global.css'],
        'scripts' => ['global.js'],
        'has_music' => false,
    ];

    public static function assertRequestMethod(string ...$allowedMethods): void
    {
        if (!in_array($_SERVER['REQUEST_METHOD'] ?? '', $allowedMethods, true)) {
            ResponseHelper::error('Method Not Allowed', 405);
        }
    }

    public static function begin(string $title = '', string $description = '', string $layout = 'base'): void
    {
        self::$__layout_data['title'] = $title !== '' && $title !== '0' ? $title . ' - Leek.cafe' : 'Leek.cafe';
        self::$__layout_data['description'] = $description ?: "Richard's personal site about programming, technology, and other interests.";
        self::$__layout_data['layout'] = $layout;

        ob_start();
    }

    public static function addStyleSheet(string $stylesheet): void
    {
        if (in_array($stylesheet, self::$__layout_data['stylesheets'], true)) {
            return;
        }

        self::$__layout_data['stylesheets'][] = $stylesheet;
    }

    /**
     * @return string[] List of stylesheets to include in the layout
     */
    public static function getStyleSheets(): array
    {
        return self::$__layout_data['stylesheets'];
    }

    public static function addScript(string $script): void
    {
        if (in_array($script, self::$__layout_data['scripts'], true)) {
            return;
        }

        self::$__layout_data['scripts'][] = $script;
    }

    /**
     * @return string[] List of scripts to include in the layout
     */
    public static function getScripts(): array
    {
        return self::$__layout_data['scripts'];
    }

    public static function enableMusic(): void
    {
        self::$__layout_data['has_music'] = true;
    }

    public static function hasMusic(): bool
    {
        return self::$__layout_data['has_music'];
    }

    public static function addMusic(string $path, string $type = "audio/webm"): void
    {
        self::$__layout_data['has_music'] = true;
        echo sprintf(
            '<audio autoplay loop><source src="%s" type="%s"></audio>',
            htmlspecialchars($path),
            htmlspecialchars($type),
        );

        self::addStyleSheet('autoplay.css');
    }

    /**
     * Get the URL for a stylesheet, handling development vs production environments.
     * In development, it will point to the esbuild dev server. In production, it will point to the public directory.
     * @param string $stylesheet The name of the stylesheet (e.g., "global.css")
     * @return string The URL to the stylesheet
     */
    public static function getStyleSheetUrl(string $stylesheet): string
    {
        if ($_ENV['APP_ENV'] === 'development') {
            $host = $_ENV['ESBUILD_SERVE_HOST'] ?? 'localhost';
            $port = $_ENV['ESBUILD_SERVE_PORT'] ?? 3751;
            return sprintf('http://%s:%s/css/%s', $host, $port, $stylesheet);
        }

        return '/css/' . $stylesheet;
    }

    public static function end(): void
    {
        self::$__layout_data['content'] = ob_get_clean() ?: '';
        $viewLayout = self::$__layout_data['layout'];
        require __DIR__ . '/../Layouts/' . $viewLayout . '.php';
    }

    public static function getTitle(): string
    {
        return htmlspecialchars(self::$__layout_data['title']);
    }

    public static function getDescription(): string
    {
        return htmlspecialchars(self::$__layout_data['description']);
    }

    public static function getContent(): string
    {
        return self::$__layout_data['content'];
    }

    /**
     * Check if the given route is active based on the current request URI.
     * This can be used to add an "active" class to navigation links.
     * @param string $route The route to check (e.g., "/about")
     * @return bool True if the current request URI contains the route, false otherwise
     */
    public static function is_active_route(string $route): bool
    {
        $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '/';
        if (!$currentPath) {
            return false;
        }

        if ($route === '/') {
            return $currentPath === '/';
        }

        return str_contains($currentPath, $route);
    }

    public static function getTheme(): string
    {
        $cookie = $_COOKIE['theme'] ?? 'light';
        return in_array($cookie, ['light', 'dark'], true) ? $cookie : 'light';
    }
}
