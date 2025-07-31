<?php
/*
 * NamelessSentry Module Init - Ultra simple version
 */

error_log('NamelessSentry: init.php started');
error_log('NamelessSentry: Available variables: ' . implode(', ', array_keys(get_defined_vars())));

try {
    if (!file_exists(__DIR__ . '/module.php')) {
        error_log('NamelessSentry: module.php not found');
        return;
    }

    error_log('NamelessSentry: about to require module.php');
    require_once(__DIR__ . '/module.php');
    error_log('NamelessSentry: module.php required successfully');
    
    if (!class_exists('NamelessSentry_Module')) {
        error_log('NamelessSentry: class not found');
        return;
    }

    error_log('NamelessSentry: about to instantiate module');
    // Try with null values first to see if it works
    $module = new NamelessSentry_Module(null, null);
    error_log('NamelessSentry: module instantiated successfully');
    
} catch (Exception $e) {
    error_log('NamelessSentry Exception: ' . $e->getMessage());
} catch (Error $e) {
    error_log('NamelessSentry Error: ' . $e->getMessage());
} catch (Throwable $e) {
    error_log('NamelessSentry Throwable: ' . $e->getMessage());
}

error_log('NamelessSentry: init.php completed');
?>
