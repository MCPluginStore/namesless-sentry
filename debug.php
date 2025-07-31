<?php
// Simple diagnostic script
echo "<h2>Sentry Module Debug Information</h2>";

// Check PHP version
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";

// Check if files exist
$files = [
    'init.php',
    'module.php',
    'SentryIntegration.php',
    'composer.json',
    'vendor/autoload.php'
];

echo "<h3>File Status:</h3>";
foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    $exists = file_exists($path);
    $status = $exists ? "✅ EXISTS" : "❌ MISSING";
    echo "<p><strong>$file:</strong> $status</p>";
    
    if ($exists && $file === 'module.php') {
        // Try to load the module class
        try {
            require_once($path);
            if (class_exists('NamelessSentry_Module')) {
                echo "<p>✅ NamelessSentry_Module class loads successfully</p>";
            } else {
                echo "<p>❌ NamelessSentry_Module class not found after requiring file</p>";
            }
        } catch (Exception $e) {
            echo "<p>❌ Error loading module.php: " . htmlspecialchars($e->getMessage()) . "</p>";
        } catch (Error $e) {
            echo "<p>❌ Fatal error loading module.php: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}

// Check PHP error reporting
echo "<h3>PHP Error Reporting:</h3>";
echo "<p><strong>Error Reporting Level:</strong> " . error_reporting() . "</p>";
echo "<p><strong>Display Errors:</strong> " . (ini_get('display_errors') ? 'ON' : 'OFF') . "</p>";
echo "<p><strong>Log Errors:</strong> " . (ini_get('log_errors') ? 'ON' : 'OFF') . "</p>";
echo "<p><strong>Error Log File:</strong> " . (ini_get('error_log') ?: 'Not set') . "</p>";

// Show recent PHP errors if error log exists
$error_log = ini_get('error_log');
if ($error_log && file_exists($error_log)) {
    echo "<h3>Recent PHP Errors (last 20 lines):</h3>";
    $lines = file($error_log);
    $recent_lines = array_slice($lines, -20);
    echo "<pre style='background: #f5f5f5; padding: 10px; overflow-x: auto;'>";
    foreach ($recent_lines as $line) {
        echo htmlspecialchars($line);
    }
    echo "</pre>";
}

echo "<h3>Instructions:</h3>";
echo "<p>1. Access this script directly in your browser: <code>http://yoursite.com/path/to/sentry/debug.php</code></p>";
echo "<p>2. Check your web server error logs for more details</p>";
echo "<p>3. Enable PHP error display temporarily by adding this to your .htaccess or PHP file:</p>";
echo "<pre>ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);</pre>";
?>
