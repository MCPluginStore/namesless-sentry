<?php
use Sentry\SentrySdk;

class NamelessSentry_Module extends Module
{
    public function __construct()
    {
        // Setup Monolog logger with Sentry handler
        $this->logger = new Logger('sentry');
        $this->logger->pushHandler(new SentryHandler(SentrySdk::getCurrentHub(), Logger::ERROR));
    }

    // Example NamelessMC required methods
    public function onInstall() {}
    public function onUninstall() {}
    public function onEnable() {}
    public function onDisable() {}
    public function onPageLoad() {}
    public function getDebugInfo() { return ['status' => 'ok']; }

    // Example logging usage
    public function logError($message, $context = []) {
        $this->logger->error($message, $context);
    }
    {
        try {
            // Load Composer autoload for Sentry SDK
            require_once __DIR__ . '/vendor/autoload.php';

            // Initialize Sentry
            \Sentry\init([
                'dsn' => getenv('SENTRY_DSN') ?: '', // Make sure you set this environment variable
                'environment' => defined('ENVIRONMENT') ? ENVIRONMENT : 'production',
                'release' => 'namelessmc@' . (defined('NAMELESS_VERSION') ? NAMELESS_VERSION : 'unknown'),
                'error_types' => E_ALL,
            ]);

            // Set error and exception handlers to forward to Sentry
            set_exception_handler(function ($exception) {
                \Sentry\captureException($exception);
            });

            set_error_handler(function ($severity, $message, $file, $line) {
                \Sentry\captureMessage("[PHP $severity] $message in $file:$line");
            });
        } catch (Throwable $e) {
            error_log('[NamelessSentry] Initialization failed: ' . $e->getMessage());
        }
    }

    public function onInstall()
    {
        return true;
    }

    public function onUninstall()
    {
        return true;
    }

    public function onEnable()
    {
        return true;
    }

    public function onDisable()
    {
        return true;
    }

    public function onPageLoad()
    {
        // Required empty method
    }

    public function getDebugInfo()
    {
        return ['version' => '1.0.0'];
    }
    {
        return ['version' => '1.0.0'];
    }
}
