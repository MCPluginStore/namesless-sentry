<?php
class NamelessSentry_Module extends Module {
    
    public function __construct($module, $pages) {
        // Don't log anything in constructor to avoid potential issues
        // Just do the absolute minimum
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
