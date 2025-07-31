<?php

class NamelessSentry_Module extends Module
{
    public function __construct($module, $pages)
    {
        // Initialize Sentry safely
        $this->initializeSentry();
    }

    private function initializeSentry()
    {
        try {
            // Check if config exists
            $config_file = __DIR__ . '/config.php';
            if (!file_exists($config_file)) {
                return; // No config, skip initialization
            }

            // Check if vendor exists
            if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
                return; // No dependencies, skip initialization
            }

            // Load dependencies
            require_once(__DIR__ . '/vendor/autoload.php');
            
            // Get config
            $config = require($config_file);
            if (empty($config['dsn'])) {
                return; // No DSN configured
            }

            // Initialize Sentry
            \Sentry\init([
                'dsn' => $config['dsn'],
                'environment' => $config['environment'] ?? 'production',
                'traces_sample_rate' => $config['traces_sample_rate'] ?? 0.1,
            ]);

            // Initialize Monolog if available
            if (class_exists('Monolog\Logger')) {
                $logger = new \Monolog\Logger('sentry');
                $sentryHandler = new \Sentry\Monolog\Handler(
                    \Sentry\SentrySdk::getCurrentHub(),
                    \Monolog\Logger::ERROR
                );
                $logger->pushHandler($sentryHandler);
            }

        } catch (Exception $e) {
            // Silently fail - don't break the site
            error_log('Sentry initialization error: ' . $e->getMessage());
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
        // Set user context if user is logged in
        try {
            if (isset($GLOBALS['user']) && method_exists($GLOBALS['user'], 'isLoggedIn') && $GLOBALS['user']->isLoggedIn()) {
                $userData = $GLOBALS['user']->data();
                if ($userData) {
                    \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($userData): void {
                        $scope->setUser([
                            'id' => $userData->id ?? null,
                            'username' => $userData->username ?? null,
                        ]);
                    });
                }
            }
        } catch (Exception $e) {
            // Silently fail
        }
    }

    public function getDebugInfo(): array
    {
        return array('version' => '1.0.0');
    }
}
