<?php
class NamelessSentry_Module extends Module {
    
    public function __construct($module, $pages) {
        // Initialize Sentry safely
        $this->initializeSentry();
    }

    private function initializeSentry() {
        try {
            // Only initialize if vendor directory exists
            if (file_exists(__DIR__ . '/vendor/autoload.php')) {
                require_once(__DIR__ . '/vendor/autoload.php');
                require_once(__DIR__ . '/SentryIntegration.php');
                
                // Initialize Sentry
                \SentryIntegration\SentryIntegration::init();
            }
        } catch (Exception $e) {
            // Silently fail to avoid breaking the site
            error_log('Sentry Module Error: ' . $e->getMessage());
        } catch (Error $e) {
            // Also catch fatal errors
            error_log('Sentry Module Fatal Error: ' . $e->getMessage());
        }
    }

    public function onInstall() {
        return true;
    }

    public function onUninstall() {
        return true;
    }

    public function onEnable() {
        return true;
    }

    public function onDisable() {
        return true;
    }

    public function onPageLoad() {
        // Required empty method
    }

    public function getDebugInfo(): array {
        return ['version' => '1.0.0'];
    }
}
