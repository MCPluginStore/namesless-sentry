<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/module.php';

$config = require __DIR__ . '/config.php';
if (!empty($config['sentry_dsn'])) {
    Sentry\init([
        'dsn' => $config['sentry_dsn'],
        'environment' => $config['environment'],
    ]);
}
