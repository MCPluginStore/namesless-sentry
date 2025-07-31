<?php
class NamelessSentry_Module extends Module {
    
    public function __construct($module, $pages) {
        // Test version - log when module loads
        error_log('NamelessSentry Module: Constructor called');
        
        // Try to initialize Sentry with maximum safety
        $this->safeSentryInit();
    }

    private function safeSentryInit() {
        try {
            error_log('NamelessSentry Module: Attempting Sentry init');
            
            // Check if config file exists
            $config_file = __DIR__ . '/config.php';
            if (!file_exists($config_file)) {
                error_log('NamelessSentry Module: No config.php file found');
                return;
            }
            
            // Check if vendor exists
            if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
                error_log('NamelessSentry Module: No vendor/autoload.php found');
                return;
            }
            
            // Load dependencies
            require_once(__DIR__ . '/vendor/autoload.php');
            
            // Get config
            $config = require($config_file);
            if (empty($config['dsn'])) {
                error_log('NamelessSentry Module: No DSN configured');
                return;
            }
            
            // Initialize Sentry directly (bypass our class for now)
            \Sentry\init([
                'dsn' => $config['dsn'],
                'environment' => $config['environment'] ?? 'production',
            ]);
            
            error_log('NamelessSentry Module: Sentry initialized successfully');
            
        } catch (Exception $e) {
            error_log('NamelessSentry Module Exception: ' . $e->getMessage());
        } catch (Error $e) {
            error_log('NamelessSentry Module Fatal Error: ' . $e->getMessage());
        } catch (Throwable $e) {
            error_log('NamelessSentry Module Throwable: ' . $e->getMessage());
        }
    }

    public function onInstall() {
        error_log('NamelessSentry Module: onInstall called');
        return true;
    }

    public function onUninstall() {
        error_log('NamelessSentry Module: onUninstall called');
        return true;
    }

    public function onEnable() {
        error_log('NamelessSentry Module: onEnable called');
        return true;
    }

    public function onDisable() {
        error_log('NamelessSentry Module: onDisable called');
        return true;
    }

    public function onPageLoad() {
        // Required empty method
    }

    public function getDebugInfo(): array {
        return ['version' => '1.0.0'];
    }
}
