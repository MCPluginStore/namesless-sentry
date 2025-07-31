<?php
class SentryIntegration_Module extends Module {
    private $_module;

    public function __construct($module, $pages) {
        $this->_module = $module;

        // Load config
        require_once(__DIR__ . '/SentryIntegration.php');
        \SentryIntegration\SentryIntegration::init();
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

    public function getDebugInfo(): array {
        return [
            'version' => '1.0.0',
            'installed' => true
        ];
    }
}
