<?php

// Check if Module class exists before trying to extend it
if (!class_exists('Module')) {
    error_log('NamelessSentry: Module class not available when loading module.php');
    return;
}

class NamelessSentry_Module extends Module
{
    public function __construct($module, $pages)
    {
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
