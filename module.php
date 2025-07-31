<?php
use Monolog\Logger;
use Sentry\Monolog\Handler as SentryHandler;
use Sentry\SentrySdk;

class NamelessSentry_Module extends Module {
    private $logger;

    public function __construct() {
        $name = 'NamelessSentry';
        $author = 'Enno Gelhaus';
        $module_version = '1.0.0';
        $nameless_version = '2.2.0';

        parent::__construct($this, $name, $author, $module_version, $nameless_version);

        // Sentry initialization (early, using config.php if available)
        if (class_exists('Sentry\\init')) {
            $sentryDsn = null;
            $sentryEnv = 'production';
            if (file_exists(__DIR__ . '/config.php')) {
                $config = include(__DIR__ . '/config.php');
                if (isset($config['sentry_dsn'])) {
                    $sentryDsn = $config['sentry_dsn'];
                }
                if (isset($config['sentry_env'])) {
                    $sentryEnv = $config['sentry_env'];
                }
            }
            if ($sentryDsn) {
                \Sentry\init([
                    'dsn' => $sentryDsn,
                    'environment' => $sentryEnv,
                ]);
            }
        }

        // Setup Monolog logger with Sentry handler (safe, only if dependencies exist)
        if (class_exists('Monolog\\Logger') && class_exists('Sentry\\Monolog\\Handler')) {
            $this->logger = new Logger('sentry');
            // Forward all logs from DEBUG and above to Sentry
            $this->logger->pushHandler(new SentryHandler(SentrySdk::getCurrentHub(), Logger::DEBUG));
        }
    }

    public function onInstall() {}
    public function onUninstall() {}
    public function onEnable() {}
    public function onDisable() {}
    public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template) {}
    public function getDebugInfo(): array {
        return [];
    }

    // Logging helpers for Sentry/Monolog
    public function logDebug($message, $context = []) {
        if ($this->logger) {
            $this->logger->debug($message, $context);
        }
    }
    public function logInfo($message, $context = []) {
        if ($this->logger) {
            $this->logger->info($message, $context);
        }
    }
    public function logWarning($message, $context = []) {
        if ($this->logger) {
            $this->logger->warning($message, $context);
        }
    }
    public function logError($message, $context = []) {
        if ($this->logger) {
            $this->logger->error($message, $context);
        }
    }
    public function logCritical($message, $context = []) {
        if ($this->logger) {
            $this->logger->critical($message, $context);
        }
    }
}
