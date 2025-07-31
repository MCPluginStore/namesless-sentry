<?php
/*
 * Ultra-safe init.php - maximum error handling
 */

try {
    // Check if module.php exists
    if (!file_exists(__DIR__ . '/module.php')) {
        error_log('NamelessSentry: module.php not found');
        return;
    }

    // Load the module class with error handling
    require_once(__DIR__ . '/module.php');
    
    // Check if class exists
    if (!class_exists('NamelessSentry_Module')) {
        error_log('NamelessSentry: Module class not found');
        return;
    }

    // Check if required parameters exist
    if (!isset($this) || !isset($pages)) {
        error_log('NamelessSentry: Required NamelessMC variables not available');
        return;
    }

    // Initialize the module
    $module = new NamelessSentry_Module($this, $pages);
    
} catch (Exception $e) {
    error_log('NamelessSentry Init Exception: ' . $e->getMessage());
} catch (Error $e) {
    error_log('NamelessSentry Init Fatal Error: ' . $e->getMessage());
} catch (Throwable $e) {
    error_log('NamelessSentry Init Throwable: ' . $e->getMessage());
}
?>
