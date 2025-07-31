<?php
class NamelessSentry_Module extends Module {
    
    public function __construct($module, $pages) {
        // Do absolutely NOTHING in constructor
        // Moved everything to onEnable to prevent loading issues
    }

    public function onInstall() {
        return true;
    }

    public function onUninstall() {
        return true;
    }

    public function onEnable() {
        // Only initialize when explicitly enabled
        try {
            $this->initializeSentry();
        } catch (Exception $e) {
            error_log('Sentry Enable Error: ' . $e->getMessage());
        }
        return true;
    }

    public function onDisable() {
        return true;
    }

    public function onPageLoad() {
        // Required empty method
    }

    private function initializeSentry() {
        // Check if all required files exist
        if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
            return;
        }
        
        if (!file_exists(__DIR__ . '/config.php')) {
            return;
        }

        try {
            require_once(__DIR__ . '/vendor/autoload.php');
            $config = require(__DIR__ . '/config.php');
            
            if (!empty($config['dsn'])) {
                \Sentry\init([
                    'dsn' => $config['dsn'],
                    'environment' => $config['environment'] ?? 'production',
                ]);
            }
        } catch (Exception $e) {
            error_log('Sentry Init Error: ' . $e->getMessage());
        }
    }

    public function getDebugInfo(): array {
        return ['version' => '1.0.0'];
    }
}
