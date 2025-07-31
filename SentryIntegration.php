<?php
namespace SentryIntegration;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Sentry\Integration\MonologIntegration;
use Sentry\Monolog\Handler as SentryHandler;
use Sentry\UserFeedback;

class SentryIntegration {
    private static $logger = null;

    public static function init() {
        if (!getenv('SENTRY_DSN')) {
            return;
        }

        \Sentry\init([
            'dsn' => getenv('SENTRY_DSN'),
            'environment' => defined('ENVIRONMENT') ? ENVIRONMENT : 'production',
            'release' => 'namelessmc@' . NAMELESS_VERSION,
            'error_types' => E_ALL,
            'integrations' => [
                new MonologIntegration(
                    Logger::ERROR, // Only capture ERROR and above as issues
                    true, // Capture context
                    true  // Capture extra data
                ),
            ],
            // Session Replay configuration
            'traces_sample_rate' => 0.1, // Capture 10% of transactions for performance monitoring
            'replays_session_sample_rate' => 0.1, // Capture 10% of sessions for replay
            'replays_on_error_sample_rate' => 1.0, // Always capture replay when there's an error
            // Configure which errors create issues vs just breadcrumbs
            'before_send' => function (\Sentry\Event $event, ?\Sentry\EventHint $hint): ?\Sentry\Event {
                // Only send events that are ERROR level or above as issues
                if ($event->getLevel() && $event->getLevel()->value < \Sentry\Severity::error()->value) {
                    return null; // Don't send as issue, but still logs as breadcrumb
                }
                return $event;
            }
        ]);

        // Create a logger instance with Sentry handler
        self::$logger = new Logger('namelessmc');
        
        // Add Sentry handler for ERROR and above (creates issues)
        $sentryHandler = new SentryHandler(
            \Sentry\SentrySdk::getCurrentHub(),
            Logger::ERROR // Only ERROR and above go to Sentry as issues
        );
        self::$logger->pushHandler($sentryHandler);

        // Optionally add a local file handler for all levels
        if (defined('ROOT_PATH')) {
            $fileHandler = new StreamHandler(ROOT_PATH . '/cache/logs/app.log', Logger::DEBUG);
            self::$logger->pushHandler($fileHandler);
        }

        set_exception_handler(function ($exception) {
            \Sentry\captureException($exception);
            if (self::$logger) {
                self::$logger->error('Uncaught exception: ' . $exception->getMessage(), [
                    'exception' => $exception,
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTraceAsString()
                ]);
            }
        });

        set_error_handler(function ($severity, $message, $file, $line) {
            $level = self::getLogLevelFromPhpError($severity);
            
            if (self::$logger) {
                self::$logger->log($level, $message, [
                    'php_error_severity' => $severity,
                    'file' => $file,
                    'line' => $line
                ]);
            }
            
            // For backwards compatibility, still capture severe errors directly
            if ($severity & (E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR)) {
                \Sentry\captureMessage("[PHP ERROR $severity] $message in $file:$line", \Sentry\Severity::error());
            }
        });
    }

    /**
     * Get a logger instance for application use
     */
    public static function getLogger(): ?Logger {
        return self::$logger;
    }

    /**
     * Convert PHP error severity to Monolog level
     */
    private static function getLogLevelFromPhpError(int $severity): int {
        switch ($severity) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                return Logger::ERROR;
            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
                return Logger::WARNING;
            case E_NOTICE:
            case E_USER_NOTICE:
                return Logger::NOTICE;
            case E_STRICT:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                return Logger::INFO;
            default:
                return Logger::DEBUG;
        }
    }

    /**
     * Helper methods for logging at different levels
     */
    public static function logDebug(string $message, array $context = []): void {
        if (self::$logger) {
            self::$logger->debug($message, $context);
        }
    }

    public static function logInfo(string $message, array $context = []): void {
        if (self::$logger) {
            self::$logger->info($message, $context);
        }
    }

    public static function logWarning(string $message, array $context = []): void {
        if (self::$logger) {
            self::$logger->warning($message, $context);
        }
    }

    public static function logError(string $message, array $context = []): void {
        if (self::$logger) {
            self::$logger->error($message, $context);
        }
    }

    /**
     * Capture user feedback for an event
     * 
     * @param string $eventId The event ID from Sentry (can be obtained from captureException/captureMessage)
     * @param string $name User's name
     * @param string $email User's email
     * @param string $comments User's feedback comments
     */
    public static function captureUserFeedback(string $eventId, string $name, string $email, string $comments): void {
        $userFeedback = new UserFeedback($eventId, $name, $email, $comments);
        \Sentry\captureUserFeedback($userFeedback);
    }

    /**
     * Set user context for better error tracking
     * 
     * @param array $userData User data array with keys like 'id', 'email', 'username', etc.
     */
    public static function setUserContext(array $userData): void {
        \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($userData): void {
            $scope->setUser($userData);
        });
    }

    /**
     * Add extra context to Sentry events
     * 
     * @param string $key Context key
     * @param mixed $value Context value
     */
    public static function setExtraContext(string $key, $value): void {
        \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($key, $value): void {
            $scope->setExtra($key, $value);
        });
    }

    /**
     * Add tags to Sentry events for better filtering
     * 
     * @param array $tags Array of key-value pairs
     */
    public static function setTags(array $tags): void {
        \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($tags): void {
            foreach ($tags as $key => $value) {
                $scope->setTag($key, $value);
            }
        });
    }

    /**
     * Start a new transaction for performance monitoring
     * 
     * @param string $name Transaction name
     * @param string $op Operation type (e.g., 'http.request', 'db.query')
     * @return \Sentry\Tracing\Transaction|null
     */
    public static function startTransaction(string $name, string $op = 'task'): ?\Sentry\Tracing\Transaction {
        $transactionContext = new \Sentry\Tracing\TransactionContext();
        $transactionContext->setName($name);
        $transactionContext->setOp($op);
        
        return \Sentry\SentrySdk::getCurrentHub()->startTransaction($transactionContext);
    }

    /**
     * Add breadcrumb for better error context
     * 
     * @param string $message Breadcrumb message
     * @param string $category Category (e.g., 'auth', 'navigation', 'http')
     * @param string $level Level (debug, info, warning, error)
     * @param array $data Additional data
     */
    public static function addBreadcrumb(string $message, string $category = 'custom', string $level = 'info', array $data = []): void {
        \Sentry\addBreadcrumb(
            new \Sentry\Breadcrumb($level, 'default', $category, $message, $data)
        );
    }

    /**
     * Generate JavaScript configuration for frontend Sentry integration
     * This should be included in your HTML template to enable browser-side error tracking
     * 
     * @param bool $includeReplay Whether to include session replay
     * @param bool $includeFeedback Whether to include user feedback widget
     * @return string JavaScript code to initialize Sentry in the browser
     */
    public static function getJavaScriptConfig(bool $includeReplay = true, bool $includeFeedback = true): string {
        $dsn = getenv('SENTRY_DSN');
        if (!$dsn) {
            return '// Sentry DSN not configured';
        }

        $environment = defined('ENVIRONMENT') ? ENVIRONMENT : 'production';
        $release = 'namelessmc@' . NAMELESS_VERSION;

        $integrations = [];
        
        if ($includeFeedback) {
            $integrations[] = 'Sentry.feedbackIntegration({
            colorScheme: "system",
            isNameRequired: true,
            isEmailRequired: true,
            showBranding: false,
            themeDark: {
                background: "#2b2b2b",
                backgroundHover: "#3a3a3a",
                foreground: "#ced4da",
                border: "#495057"
            },
            themeLight: {
                background: "#ffffff",
                backgroundHover: "#f8f9fa",
                foreground: "#212529",
                border: "#dee2e6"
            }
        })';
        }

        if ($includeReplay) {
            $integrations[] = 'Sentry.replayIntegration()';
        }

        $integrationsStr = !empty($integrations) ? '[' . implode(',', $integrations) . ']' : '[]';

        // Extract the project ID from DSN for the loader script
        $projectId = '';
        if (preg_match('/https:\/\/([a-f0-9]+)@([^\/]+)\/(\d+)/', $dsn, $matches)) {
            $projectId = $matches[1];
        }

        return <<<JAVASCRIPT
<script
  src="https://js-de.sentry-cdn.com/{$projectId}.min.js"
  crossorigin="anonymous"
></script>
<script>
window.sentryOnLoad = function() {
    Sentry.init({
        environment: "{$environment}",
        release: "{$release}",
        sendDefaultPii: true,
        // Session Replay - 10% sample rate in production, 100% on errors
        replaysSessionSampleRate: 0.1,
        replaysOnErrorSampleRate: 1.0,
        // Performance monitoring
        tracesSampleRate: 0.1,
        beforeSend: function(event, hint) {
            // Only send errors and above, filter out lower level events
            if (event.level && ['debug', 'info'].includes(event.level)) {
                return null;
            }
            return event;
        }
    });

    // Set user context if available
    if (typeof NAMELESS_USER !== 'undefined' && NAMELESS_USER) {
        Sentry.setUser({
            id: NAMELESS_USER.id,
            username: NAMELESS_USER.username,
            email: NAMELESS_USER.email
        });
    }
JAVASCRIPT;

        // Add lazy loading integrations
        if ($includeFeedback) {
            $javascript .= <<<JAVASCRIPT

    // Lazy load feedback integration
    Sentry.lazyLoadIntegration("feedbackIntegration")
        .then((feedbackIntegration) => {
            Sentry.addIntegration(feedbackIntegration({
                colorScheme: "system",
                isNameRequired: true,
                isEmailRequired: true,
                showBranding: false,
                themeDark: {
                    background: "#2b2b2b",
                    backgroundHover: "#3a3a3a",
                    foreground: "#ced4da",
                    border: "#495057"
                },
                themeLight: {
                    background: "#ffffff",
                    backgroundHover: "#f8f9fa",
                    foreground: "#212529",
                    border: "#dee2e6"
                }
            }));
        })
        .catch(() => {
            // Feedback integration failed to load - silently ignore
            console.warn('Sentry feedback integration failed to load');
        });
JAVASCRIPT;
        }

        if ($includeReplay) {
            $javascript .= <<<JAVASCRIPT

    // Lazy load replay integration
    Sentry.lazyLoadIntegration("replayIntegration")
        .then((replayIntegration) => {
            Sentry.addIntegration(replayIntegration({
                maskAllText: false,
                blockAllMedia: false,
                maskAllInputs: false
            }));
        })
        .catch(() => {
            // Replay integration failed to load - silently ignore
            console.warn('Sentry replay integration failed to load');
        });
JAVASCRIPT;
        }

        $javascript .= <<<JAVASCRIPT
};
</script>
JAVASCRIPT;

        return $javascript;
    }

    /**
     * Generate a simple JavaScript snippet to include in templates
     * Call this in your template's <head> section
     */
    public static function renderJavaScriptIntegration(): void {
        echo self::getJavaScriptConfig();
    }
}
