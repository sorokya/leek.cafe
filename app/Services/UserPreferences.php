<?php

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
    public function parse_from_request(Request $request)
    {
        $value = $request->cookie(self::COOKIE_NAME);

        if (!is_string($value) || $value === '') {
            return;
        }

        $decoded = json_decode($value, true);
        if (!is_array($decoded)) {
            return;
        }

        $theme_str = $decoded['theme'] ?? 'system';
        $this->theme = Theme::tryFrom($theme_str) ?? Theme::System;
    }

    /**
     * Toggles the current theme from light to dark or dark to light.
     */
    public function toggleTheme()
    {
        $this->theme = $this->theme === Theme::Dark ? Theme::Light : Theme::Dark;
    }

    /**
     * Returns the cookie representing the user preferences.
     *
     * @param Request $request
     * @return HttpFoundationCookie
     */
    public function get_cookie(Request $request): HttpFoundationCookie
    {
        return Cookie::make(
            name: self::COOKIE_NAME,
            value: json_encode([
                'theme' => $this->theme->value
            ]),
            minutes: 60 * 24 * 365,
            path: '/',
            secure: $request->isSecure(),
            httpOnly: true,
            sameSite: 'lax',
        );
    }
}
