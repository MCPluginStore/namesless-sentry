<?php

class NamelessSentry_Module extends Module
{
    public function __construct($module, $pages)
    {
        // Minimal constructor
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
        // Required empty method
    }

    public function getDebugInfo(): array
    {
        return array('version' => '1.0.0');
    }
}
