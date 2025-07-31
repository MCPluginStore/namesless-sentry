<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
}
