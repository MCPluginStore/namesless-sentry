<?php
class NamelessSentry_Module extends Module {
    private $_module;

    public function __construct($module, $pages) {
        $this->_module = $module;

        try {
            // Load Composer autoload first
            if (file_exists(__DIR__ . '/vendor/autoload.php')) {
                require_once(__DIR__ . '/vendor/autoload.php');
            }

            // Load config
            require_once(__DIR__ . '/SentryIntegration.php');
            
            // Initialize Sentry (with error handling)
            \SentryIntegration\SentryIntegration::init();

            // Add settings page
            $pages->add('SentryIntegration', '/panel/core/sentry_settings', 'pages/core/sentry_settings.php');

            // Hook into template rendering for frontend integration
            if (class_exists('EventHandler')) {
                EventHandler::registerListener('renderTemplate', [$this, 'onRenderTemplate']);
            }
        } catch (Exception $e) {
            // Log error but don't break the site
            error_log('Sentry Integration Module Error: ' . $e->getMessage());
        }
    }

    /**
     * Called when templates are rendered - inject Sentry JavaScript
     */
    public function onRenderTemplate($params = []) {
        try {
            // Only inject on frontend pages, not admin panel
            if (defined('PANEL_PAGE')) return;

            // Check if frontend integration is enabled via database settings
            if (!class_exists('DB')) return;
            
            $db = DB::getInstance();
            if (!$db) return;
            
            $frontend_setting = $db->get('settings', ['name', '=', 'sentry_enable_frontend']);
            $enable_frontend = $frontend_setting->count() ? (bool)$frontend_setting->first()->value : false;
            
            if (!$enable_frontend) return;

            // Check if DSN is configured
            $dsn_setting = $db->get('settings', ['name', '=', 'sentry_dsn']);
            if (!$dsn_setting->count() || empty($dsn_setting->first()->value)) return;

            // Add Sentry JavaScript to template
            $sentry_js = \SentryIntegration\SentryIntegration::getJavaScriptConfig();
            
            // If using Smarty template system
            if (isset($params['smarty'])) {
                $params['smarty']->assign('SENTRY_JS', $sentry_js);
            }
        } catch (Exception $e) {
            // Silently fail to avoid breaking the site
            error_log('Sentry Integration Template Error: ' . $e->getMessage());
        }
    }

    public function onInstall() {
        // Create any necessary database tables or settings here
        return true;
    }

    public function onUninstall() {
        // Clean up settings/tables here
        return true;
    }

    public function onEnable() {
        return true;
    }

    public function onDisable() {
        return true;
    }

    public function onPageLoad() {
        try {
            // This method is called on every page load
            // We can use this to set user context or add breadcrumbs
            
            // Set user context if user is logged in
            if (isset($GLOBALS['user']) && is_object($GLOBALS['user']) && method_exists($GLOBALS['user'], 'isLoggedIn') && $GLOBALS['user']->isLoggedIn()) {
                $userData = $GLOBALS['user']->data();
                if ($userData) {
                    \SentryIntegration\SentryIntegration::setUserContext([
                        'id' => $userData->id ?? null,
                        'username' => $userData->username ?? null,
                        'email' => $userData->email ?? null
                    ]);
                }
            }
        } catch (Exception $e) {
            // Silently fail to avoid breaking the site
            error_log('Sentry Integration PageLoad Error: ' . $e->getMessage());
        }
    }

    public function getDebugInfo(): array {
        return [
            'version' => '1.0.0',
            'installed' => true,
            'sentry_dsn_configured' => !empty(getenv('SENTRY_DSN')),
            'php_version' => PHP_VERSION
        ];
    }
}
