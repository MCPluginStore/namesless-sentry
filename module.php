<?php
class NamelessSentry_Module extends Module {
    private $_module;

    public function __construct($module, $pages) {
        $this->_module = $module;

        // Load config
        require_once(__DIR__ . '/SentryIntegration.php');
        \SentryIntegration\SentryIntegration::init();

        // Add settings page
        $pages->add('SentryIntegration', '/panel/core/sentry_settings', 'pages/core/sentry_settings.php');

        // Hook into template rendering for frontend integration
        EventHandler::registerListener('renderTemplate', [$this, 'onRenderTemplate']);
    }

    /**
     * Called when templates are rendered - inject Sentry JavaScript
     */
    public function onRenderTemplate($params = []) {
        // Only inject on frontend pages, not admin panel
        if (defined('PANEL_PAGE')) return;

        // Check if frontend integration is enabled via database settings
        try {
            $db = DB::getInstance();
            $frontend_setting = $db->get('settings', ['name', '=', 'sentry_enable_frontend']);
            $enable_frontend = $frontend_setting->count() ? (bool)$frontend_setting->first()->value : true;
            
            if (!$enable_frontend) return;

            // Check if DSN is configured
            $dsn_setting = $db->get('settings', ['name', '=', 'sentry_dsn']);
            if (!$dsn_setting->count() || empty($dsn_setting->first()->value)) return;

        } catch (Exception $e) {
            // If database access fails, skip frontend integration
            return;
        }

        // Add Sentry JavaScript to template
        $sentry_js = \SentryIntegration\SentryIntegration::getJavaScriptConfig();
        
        // If using Smarty template system
        if (isset($params['smarty'])) {
            $params['smarty']->assign('SENTRY_JS', $sentry_js);
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

    public function getDebugInfo(): array {
        return [
            'version' => '1.0.0',
            'installed' => true,
            'sentry_dsn_configured' => !empty(getenv('SENTRY_DSN')),
            'php_version' => PHP_VERSION
        ];
    }
}
