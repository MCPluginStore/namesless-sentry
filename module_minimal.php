<?php
class NamelessSentry_Module extends Module {
    
    public function __construct($module, $pages) {
        error_log('NamelessSentry: Constructor started');
        
        // Do absolutely nothing that could cause errors
        // Just log that we're alive
        
        error_log('NamelessSentry: Constructor completed successfully');
    }

    public function onInstall() {
        error_log('NamelessSentry: onInstall called');
        return true;
    }

    public function onUninstall() {
        error_log('NamelessSentry: onUninstall called');
        return true;
    }

    public function onEnable() {
        error_log('NamelessSentry: onEnable called');
        return true;
    }

    public function onDisable() {
        error_log('NamelessSentry: onDisable called');
        return true;
    }

    public function onPageLoad() {
        // Required empty method
        // Don't even log here to avoid spam
    }

    public function getDebugInfo(): array {
        return ['version' => '1.0.0'];
    }
}
