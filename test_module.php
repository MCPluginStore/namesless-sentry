<?php
/**
 * Simple test file to check if the module loads without errors
 * Place this in your NamelessMC root directory and access via browser
 */

// Include NamelessMC core
require_once('core/init.php');

echo "<h1>Sentry Module Test</h1>";

try {
    // Check if module directory exists
    $module_path = ROOT_PATH . '/modules/namesless-sentry';
    if (!is_dir($module_path)) {
        echo "<p style='color: red;'>❌ Module directory not found: {$module_path}</p>";
        exit;
    }
    echo "<p style='color: green;'>✅ Module directory exists</p>";

    // Check if composer dependencies are installed
    $vendor_path = $module_path . '/vendor';
    if (!is_dir($vendor_path)) {
        echo "<p style='color: orange;'>⚠️ Composer dependencies not installed. Run 'composer install' in module directory.</p>";
    } else {
        echo "<p style='color: green;'>✅ Composer dependencies installed</p>";
    }

    // Check if autoload exists
    $autoload_path = $module_path . '/vendor/autoload.php';
    if (!file_exists($autoload_path)) {
        echo "<p style='color: orange;'>⚠️ Composer autoload not found</p>";
    } else {
        echo "<p style='color: green;'>✅ Composer autoload found</p>";
        require_once($autoload_path);
    }

    // Try to load the module class
    require_once($module_path . '/module.php');
    echo "<p style='color: green;'>✅ Module class loaded successfully</p>";

    // Try to load SentryIntegration class
    require_once($module_path . '/SentryIntegration.php');
    echo "<p style='color: green;'>✅ SentryIntegration class loaded successfully</p>";

    // Check if Sentry classes are available
    if (class_exists('\Sentry\init')) {
        echo "<p style='color: green;'>✅ Sentry SDK available</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Sentry SDK not available (this is okay if composer install wasn't run)</p>";
    }

    echo "<p style='color: green;'><strong>✅ Basic module loading test passed!</strong></p>";
    echo "<p>You can now try enabling the module in the NamelessMC admin panel.</p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p style='color: red;'>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
}
?>
