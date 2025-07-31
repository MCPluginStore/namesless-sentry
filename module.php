<?php
class NamelessSentry_Module extends Module {
    
    public function __construct($module, $pages) {
        // Do absolutely nothing that could cause errors
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
