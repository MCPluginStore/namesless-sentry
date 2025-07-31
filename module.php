<?php

error_log('NamelessSentry: module.php is being loaded');

// Simple test - don't extend anything yet
class NamelessSentry_Module 
{
    public function __construct($module, $pages)
    {
        error_log('NamelessSentry: Constructor called');
    }

    public function onInstall()
    {
        return true;
    }

    public function onUninstall()
    {
        return true;
    }

    public function onEnable()
    {
        return true;
    }

    public function onDisable()
    {
        return true;
    }

    public function onPageLoad()
    {
    }

    public function getDebugInfo(): array
    {
        return array('version' => '1.0.0');
    }
}

error_log('NamelessSentry: module.php class defined');
