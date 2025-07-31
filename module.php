<?php
class NamelessSentry_Module extends Module {
    private $_module;

    public function __construct($module, $pages) {
        $this->_module = $module;

        // Add settings page - this is the only thing we do for now
        $pages->add('SentryIntegration', '/panel/core/sentry_settings', 'pages/core/sentry_settings.php');
    }

    public function onInstall() {
        // Nothing to install for now
        return true;
    }

    public function onUninstall() {
        // Clean up settings if needed
        return true;
    }

    public function onEnable() {
        return true;
    }

    public function onDisable() {
        return true;
    }

    public function onPageLoad() {
        // Required abstract method - leave empty for now
    }

    public function getDebugInfo(): array {
        return [
            'version' => '1.0.0',
            'installed' => true,
            'php_version' => PHP_VERSION,
            'module_enabled' => true
        ];
    }
}
