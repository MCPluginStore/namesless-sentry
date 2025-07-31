<?php
/*
 * NamelessSentry Module Init
 * Ultra-safe initialization with detailed logging
 */

// Log the start of initialization
error_log('NamelessSentry: init.php started');

try {
    // Log environment info
    error_log('NamelessSentry: PHP version ' . PHP_VERSION);
    error_log('NamelessSentry: Working directory: ' . __DIR__);
    
    // Check if module.php exists
    if (!file_exists(__DIR__ . '/module.php')) {
        error_log('NamelessSentry: module.php not found in ' . __DIR__);
        return;
    }
    error_log('NamelessSentry: module.php found');

    // Load the module class with error handling
    require_once(__DIR__ . '/module.php');
    error_log('NamelessSentry: module.php loaded successfully');
    
    // Check if class exists
    if (!class_exists('NamelessSentry_Module')) {
        error_log('NamelessSentry: NamelessSentry_Module class not found after loading module.php');
        return;
    }
    error_log('NamelessSentry: NamelessSentry_Module class found');

    // Check if required parameters exist
    if (!isset($this) || !isset($pages)) {
        error_log('NamelessSentry: Required NamelessMC variables ($this or $pages) not available');
        error_log('NamelessSentry: $this isset: ' . (isset($this) ? 'yes' : 'no'));
        error_log('NamelessSentry: $pages isset: ' . (isset($pages) ? 'yes' : 'no'));
        return;
    }
    error_log('NamelessSentry: NamelessMC variables available');

    // Initialize the module
    $module = new NamelessSentry_Module($this, $pages);
    error_log('NamelessSentry: Module initialized successfully');
    
} catch (Exception $e) {
    error_log('NamelessSentry Init Exception: ' . $e->getMessage());
    error_log('NamelessSentry Exception trace: ' . $e->getTraceAsString());
} catch (Error $e) {
    error_log('NamelessSentry Init Fatal Error: ' . $e->getMessage());
    error_log('NamelessSentry Error trace: ' . $e->getTraceAsString());
} catch (Throwable $e) {
    error_log('NamelessSentry Init Throwable: ' . $e->getMessage());
    error_log('NamelessSentry Throwable trace: ' . $e->getTraceAsString());
}

error_log('NamelessSentry: init.php completed');
?>
