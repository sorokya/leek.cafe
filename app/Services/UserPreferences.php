<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Cookie as HttpFoundationCookie;

enum Theme: string
{
    case Light = 'light';
    case Dark = 'dark';
    case System = 'system';
}

final class UserPreferences
{
    private const COOKIE_NAME = 'preferences';

    public function __construct(
        public Theme $theme = Theme::System,
    ) {}

    /**
     * Parses the preferences from the request cookie.
     */
    public function parse_from_request(Request $request): void
    {
        $value = $request->cookie(self::COOKIE_NAME);

        if (! is_string($value) || $value === '') {
            return;
        }

        $decoded = json_decode($value, true);
        if (! is_array($decoded)) {
            return;
        }

        $theme_str = is_string($decoded['theme']) ? $decoded['theme'] : 'system';
        $this->theme = Theme::tryFrom($theme_str) ?? Theme::System;
    }

    /**
     * Toggles the current theme from light to dark or dark to light.
     */
    public function toggleTheme(): void
    {
        $this->theme = $this->theme === Theme::Dark ? Theme::Light : Theme::Dark;
    }

    /**
     * Returns the cookie representing the user preferences.
     */
    public function get_cookie(Request $request): HttpFoundationCookie
    {
        $json = json_encode([
            'theme' => $this->theme->value,
        ]);

        throw_unless(is_string($json), \RuntimeException::class, 'Failed to encode user preferences to JSON.');

        return Cookie::make(
            name: self::COOKIE_NAME,
            value: $json,
            minutes: 60 * 24 * 365,
            path: '/',
            secure: $request->isSecure(),
            httpOnly: true,
            sameSite: 'lax',
        );
    }
}
