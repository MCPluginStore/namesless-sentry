<?php
class NamelessSentry_Module extends Module {
    private $_module;

    public function __construct($module, $pages) {
        $this->_module = $module;

        // Add settings page (this should always work)
        $pages->add('SentryIntegration', '/panel/core/sentry_settings', 'pages/core/sentry_settings.php');
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
        // Empty implementation to satisfy abstract method
    }

    public function getDebugInfo(): array {
        return [
            'version' => '1.0.0',
            'installed' => true,
            'php_version' => PHP_VERSION
        ];
    }
}
