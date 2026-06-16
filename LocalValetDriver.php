<?php

/**
 * Herd / Valet driver for PalmOilTrace.
 *
 * This repo contains TWO CodeIgniter apps sharing one document root:
 *   - the main app  -> /index.php       (front controller for everything)
 *   - the API app   -> /api/index.php   (front controller for /api/*)
 *
 * Herd's default driver only knows about the root index.php, so /api/* 404s.
 * This driver routes each app to its own front controller, sets PATH_INFO so
 * CodeIgniter 2 can resolve the route, and serves real static files directly.
 */
class LocalValetDriver extends \Valet\Drivers\ValetDriver
{
    public function serves(string $sitePath, string $siteName, string $uri): bool
    {
        // Local dev runs PHP 7.4; silence the AWS SDK "PHP deprecated" notice
        // globally (covers every AWS client, not just the Cognito one).
        putenv('AWS_SUPPRESS_PHP_DEPRECATION_WARNING=true');
        $_ENV['AWS_SUPPRESS_PHP_DEPRECATION_WARNING'] = 'true';
        $_SERVER['AWS_SUPPRESS_PHP_DEPRECATION_WARNING'] = 'true';

        return true;
    }

    public function isStaticFile(string $sitePath, string $siteName, string $uri)
    {
        $path = $sitePath . rtrim($uri, '/');

        if ($uri !== '/' && file_exists($path) && ! is_dir($path)
            && strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'php') {
            return $path;
        }

        return false;
    }

    public function frontControllerPath(string $sitePath, string $siteName, string $uri): ?string
    {
        $qs = isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== ''
            ? '?' . $_SERVER['QUERY_STRING'] : '';

        // /api/* -> the separate API CodeIgniter app.
        // The API uses uri_protocol = REQUEST_URI, so we must hand it a clean
        // REQUEST_URI (the front-end builds e.g. /api//index.php/sme/foo).
        if (preg_match('#^/api(/.*)?$#', $uri, $m)) {
            $route = $this->routeFromUri(isset($m[1]) ? $m[1] : '');

            $_SERVER['SCRIPT_NAME']     = '/api/index.php';
            $_SERVER['SCRIPT_FILENAME'] = $sitePath . '/api/index.php';
            $_SERVER['DOCUMENT_URI']    = '/api/index.php';
            $_SERVER['PHP_SELF']        = '/api/index.php' . $route;
            $_SERVER['PATH_INFO']       = $route;
            $_SERVER['ORIG_PATH_INFO']  = $route;
            $_SERVER['REQUEST_URI']     = '/api' . $route . $qs;

            return $sitePath . '/api/index.php';
        }

        // Everything else -> the main app (uri_protocol = AUTO, uses PATH_INFO)
        $route = $this->routeFromUri($uri);

        $_SERVER['SCRIPT_NAME']     = '/index.php';
        $_SERVER['SCRIPT_FILENAME'] = $sitePath . '/index.php';
        $_SERVER['DOCUMENT_URI']    = '/index.php';
        $_SERVER['PHP_SELF']        = '/index.php' . $route;
        $_SERVER['PATH_INFO']       = $route;
        $_SERVER['ORIG_PATH_INFO']  = $route;
        $_SERVER['REQUEST_URI']     = $route . $qs;

        return $sitePath . '/index.php';
    }

    /**
     * Normalize the leftover URI into a clean CodeIgniter route (PATH_INFO):
     * collapse repeated leading slashes and strip a leading "/index.php" that
     * the front-end sometimes includes (e.g. /api//index.php/sme/foo).
     */
    private function routeFromUri(string $uri): string
    {
        $uri = '/' . ltrim($uri, '/');               // collapse leading slashes

        if (stripos($uri, '/index.php') === 0) {     // drop an explicit front controller
            $uri = '/' . ltrim(substr($uri, strlen('/index.php')), '/');
        }

        return $uri;                                 // always starts with '/'
    }
}
