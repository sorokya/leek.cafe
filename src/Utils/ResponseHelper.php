<?php

declare(strict_types=1);

namespace App\Utils;

class ResponseHelper
{
    /**
     * Sends an error response with a given message and status code, then exits the script.
     * @param string $message The error message to send in the response body.
     * @param int $statusCode The HTTP status code to set for the response (default is 400 Bad Request).
     */
    public static function error(string $message, int $statusCode = 400): never
    {
        http_response_code($statusCode);
        header('Content-Type: text/plain; charset=utf-8');
        echo $message;
        exit();
    }

    /**
     * Redirects to a given URL and exits the script.
     */
    public static function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit();
    }

    /**
     * Redirects back to the referring page or to the homepage if no referrer is available.
     */
    public static function back(): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        self::redirect($referer);
    }
}
