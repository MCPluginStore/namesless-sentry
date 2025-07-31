<?php
// Minimal and safe module initialization
if (file_exists(__DIR__ . '/module.php')) {
    try {
        require_once(__DIR__ . '/module.php');
        if (class_exists('NamelessSentry_Module')) {
            $module = new NamelessSentry_Module($this, $pages);
        }
    } catch (Exception $e) {
        error_log('Sentry Module Init Error: ' . $e->getMessage());
    } catch (Error $e) {
        error_log('Sentry Module Fatal Error: ' . $e->getMessage());
    }
}
?>
