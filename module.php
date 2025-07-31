<?php
use Monolog\Logger;
use Sentry\Monolog\Handler as SentryHandler;
use Sentry\SentrySdk;

class NamelessSentry_Module extends Module {
    private $logger;

    public function __construct() {
        $name = 'NamelessSentry';
        $author = 'YourName';
        $module_version = '1.0.0';
        $nameless_version = '2.0.0-pr13';

        parent::__construct($this, $name, $author, $module_version, $nameless_version);

        // Setup Monolog logger with Sentry handler (safe, only if dependencies exist)
        if (class_exists('Monolog\\Logger') && class_exists('Sentry\\Monolog\\Handler')) {
            $this->logger = new Logger('sentry');
            $this->logger->pushHandler(new SentryHandler(SentrySdk::getCurrentHub(), Logger::ERROR));
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

    // Optional: logging helper
    public function logError($message, $context = []) {
        if ($this->logger) {
            $this->logger->error($message, $context);
        }
    }
}
