<?php
namespace SentryIntegration;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Sentry\Integration\MonologIntegration;
use Sentry\Monolog\Handler as SentryHandler;
use Sentry\UserFeedback;

class SentryIntegration {
    private static $logger = null;
    private static $config = null;

    /**
     * Get configuration from config file
     */
    private static function getConfig(): array {
        if (self::$config === null) {
            $config_file = __DIR__ . '/config.php';
            
            if (file_exists($config_file)) {
                self::$config = require $config_file;
            } else {
                // Default configuration - no errors if config missing
                self::$config = [
                    'dsn' => '',
                    'environment' => 'production',
                    'enable_frontend' => false,
                    'enable_replay' => false,
                    'enable_feedback' => false,
                    'traces_sample_rate' => 0.1,
                    'replays_sample_rate' => 0.1,
                    'release' => '',
                ];
            }
        }
        
        return self::$config;
    }

    /**
     * Initialize Sentry
     */
    public static function init(): void {
        try {
            $config = self::getConfig();
            
            // Don't initialize if no DSN configured
            if (empty($config['dsn'])) {
                return;
            }

            // Load vendor autoload
            if (file_exists(__DIR__ . '/vendor/autoload.php')) {
                require_once(__DIR__ . '/vendor/autoload.php');
            } else {
                return; // Can't work without dependencies
            }

            // Initialize Sentry
            \Sentry\init([
                'dsn' => $config['dsn'],
                'environment' => $config['environment'],
                'traces_sample_rate' => $config['traces_sample_rate'],
                'release' => $config['release'] ?: null,
                'integrations' => [
                    new MonologIntegration(Logger::DEBUG, true),
                ],
            ]);

            // Initialize logger
            self::initLogger();
            
        } catch (\Exception $e) {
            // Silently fail - don't break the site
            error_log('Sentry initialization error: ' . $e->getMessage());
        }
    }

    /**
     * Initialize Monolog with Sentry handler
     */
    private static function initLogger(): void {
        try {
            self::$logger = new Logger('sentry');
            
            // Add Sentry handler for ERROR and above
            $sentryHandler = new SentryHandler(
                \Sentry\SentrySdk::getCurrentHub(),
                Logger::ERROR
            );
            self::$logger->pushHandler($sentryHandler);
            
            // Optionally add file handler for all logs
            $fileHandler = new StreamHandler(__DIR__ . '/logs/sentry.log', Logger::DEBUG);
            self::$logger->pushHandler($fileHandler);
            
        } catch (\Exception $e) {
            // Silently fail
            error_log('Sentry logger initialization error: ' . $e->getMessage());
        }
    }

    /**
     * Get logger instance
     */
    public static function getLogger(): ?Logger {
        if (self::$logger === null) {
            self::init();
        }
        return self::$logger;
    }

    /**
     * Set user context
     */
    public static function setUserContext(array $userData): void {
        try {
            \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($userData): void {
                $scope->setUser($userData);
            });
        } catch (\Exception $e) {
            // Silently fail
        }
    }

    /**
     * Get JavaScript configuration for frontend
     */
    public static function getJavaScriptConfig(): string {
        try {
            $config = self::getConfig();
            
            if (empty($config['dsn']) || !$config['enable_frontend']) {
                return '';
            }

            $js_config = [
                'dsn' => $config['dsn'],
                'environment' => $config['environment'],
                'tracesSampleRate' => $config['traces_sample_rate'],
            ];

            if (!empty($config['release'])) {
                $js_config['release'] = $config['release'];
            }

            $js = '<script src="https://browser.sentry-cdn.com/8.20.0/bundle.tracing.min.js" crossorigin="anonymous"></script>' . "\n";
            $js .= '<script>' . "\n";
            $js .= 'Sentry.init(' . json_encode($js_config, JSON_PRETTY_PRINT) . ');' . "\n";

            // Add replay integration if enabled
            if ($config['enable_replay']) {
                $js .= 'Sentry.addIntegration(Sentry.replayIntegration({' . "\n";
                $js .= '  sessionSampleRate: ' . $config['replays_sample_rate'] . ',' . "\n";
                $js .= '  errorSampleRate: 1.0' . "\n";
                $js .= '}));' . "\n";
            }

            // Add feedback integration if enabled
            if ($config['enable_feedback']) {
                $js .= 'Sentry.addIntegration(Sentry.feedbackIntegration({' . "\n";
                $js .= '  colorScheme: "system"' . "\n";
                $js .= '}));' . "\n";
            }

            $js .= '</script>' . "\n";

            return $js;
            
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Log a message
     */
    public static function log(string $level, string $message, array $context = []): void {
        $logger = self::getLogger();
        if ($logger) {
            $logger->log($level, $message, $context);
        }
    }
}
