<?php
if (!defined('BASE_URL')) {
    // Determine the base path dynamically so it works seamlessly on localhost (e.g. /rrsv/) and production (/)
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])) : '';
    $projRoot = str_replace('\\', '/', realpath(__DIR__ . '/..'));

    if ($docRoot && $projRoot && strpos($projRoot, $docRoot) === 0) {
        $sub = trim(substr($projRoot, strlen($docRoot)), '/');
        define('BASE_URL', $sub === '' ? '/' : '/' . $sub . '/');
    } else {
        // Fallback for localhost
        $host = $_SERVER['HTTP_HOST'] ?? '';
        if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
            define('BASE_URL', '/rrsv/');
        } else {
            define('BASE_URL', '/');
        }
    }
}
