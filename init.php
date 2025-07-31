<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/module.php';

$config = require __DIR__ . '/config.php';
if (!empty($config['sentry_dsn'])) {
    Sentry\init([
        'dsn' => $config['sentry_dsn'],
        'environment' => $config['environment'],
    ]);
}

// The previous code has been replaced with a clean module loader.
?>
