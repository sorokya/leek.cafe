<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Data\PDO;
use App\Utils\ResponseHelper;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

session_start([
    'name' => 'session_id',
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'cookie_secure' => $_SERVER['HTTPS'] ?? false,
    'cookie_lifetime' => 60 * 60 * 24 * 7, // 7 days
]);

$url = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if (!is_string($url)) {
    ResponseHelper::error('Bad Request');
}

$urlSegments = array_values(array_filter(explode('/', $url)));

$pagesDir = __DIR__ . '/../src/Pages';

$matchedFile = null;
$params = [];

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pagesDir));

foreach ($rii as $file) {
    if (!$file instanceof SplFileInfo) {
        continue;
    }

    if (!$file->isFile()) {
        continue;
    }

    if ($file->getExtension() !== 'php') {
        continue;
    }

    $relativePath = ltrim(str_replace([$pagesDir, '.php'], '', $file->getPathname()), '/');
    $routeSegments = explode('.', $relativePath);

    // normalize "index" at root to empty segment
    if (count($routeSegments) === 1 && $routeSegments[0] === 'index') {
        $routeSegments = [];
    }

    $routeParams = [];

    if (matchRoute($routeSegments, $urlSegments, $routeParams)) {
        $matchedFile = $file->getPathname();
        $params = $routeParams;
        break;
    }
}

if ($matchedFile) {
    extract($params);
    load_user();
    include $matchedFile;
} else {
    ResponseHelper::error('Not Found', 404);
}

/**
 * Matches a route pattern against the URL segments and extracts parameters.
 * Ignored case for static segments.
 * @param string[] $routeSegments
 * @param string[] $urlSegments
 * @param array<string, string> $params
 */
function matchRoute(array $routeSegments, array $urlSegments, array &$params): bool
{
    $params = [];

    if (count($routeSegments) !== count($urlSegments)) {
        return false;
    }

    foreach ($routeSegments as $i => $segment) {
        if (str_starts_with($segment, '$')) {
            $params[substr($segment, 1)] = $urlSegments[$i];
        } elseif (strcasecmp($segment, $urlSegments[$i]) !== 0) {
            return false;
        }
    }

    return true;
}

function load_user(): void
{
    if (!isset($_SESSION['session_token'])) {
        return;
    }

    $pdo = new PDO();
    $session = \App\Models\Session::findByToken($pdo, $_SESSION['session_token']);
    if (!$session instanceof \App\Models\Session || $session->expired()) {
        session_destroy();
        ResponseHelper::redirect('/login');
    }

    $user = \App\Models\User::findBySessionToken($pdo, $session->token);
    if (!$user instanceof \App\Models\User) {
        session_destroy();
        ResponseHelper::redirect('/login');
    }

    if (!isset($_SESSION['current_user'])) {
        $_SESSION['current_user'] = [
            'id' => $user->id,
            'username' => $user->username,
        ];
    }
}
