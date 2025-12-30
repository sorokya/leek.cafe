<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\UserPreferences;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ThemeController extends Controller
{
    public function __invoke(Request $request, UserPreferences $preferences): RedirectResponse
    {
        $preferences->parse_from_request($request);
        $preferences->toggleTheme();

        $previousUrl = url()->previous('/');

        return redirect()->to($previousUrl)
            ->withCookie($preferences->get_cookie($request));
    }
}
