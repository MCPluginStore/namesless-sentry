<?php
// Server-side diagnostic script for NamelessSentry module
// Place this in /var/www/test/modules/NamelessSentry/server_debug.php

echo "<h1>NamelessSentry Module Server Diagnostics</h1>";
echo "<style>body { font-family: Arial; } .error { color: red; } .success { color: green; } .warning { color: orange; }</style>";

// Check basic PHP info
echo "<h2>PHP Environment</h2>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Server:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Current Directory:</strong> " . __DIR__ . "</p>";

// Check file permissions and existence
echo "<h2>File Status</h2>";
$files = [
    'init.php',
    'module.php', 
    'module.json',
    'composer.json',
    'SentryIntegration.php',
    'vendor/autoload.php',
    'pages/core/sentry_settings.php'
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    $exists = file_exists($path);
    $readable = $exists ? is_readable($path) : false;
    $writable = $exists ? is_writable($path) : false;
    
    if ($exists && $readable) {
        echo "<p class='success'>✅ $file - EXISTS and READABLE</p>";
    } elseif ($exists) {
        echo "<p class='warning'>⚠️ $file - EXISTS but NOT READABLE</p>";
    } else {
        echo "<p class='error'>❌ $file - MISSING</p>";
    }
    
    if ($exists) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        echo "<p style='margin-left: 20px;'>Permissions: $perms</p>";
    }
}

// Try to load module class
echo "<h2>Module Class Test</h2>";

// DON'T try to load NamelessMC context - it causes hangs
echo "<p class='warning'>⚠️ Skipping NamelessMC context loading to avoid hangs</p>";

try {
    if (file_exists(__DIR__ . '/module.php')) {
        // Check PHP syntax without loading
        $output = shell_exec("php -l " . escapeshellarg(__DIR__ . '/module.php') . " 2>&1");
        if (strpos($output, 'No syntax errors') !== false) {
            echo "<p class='success'>✅ module.php syntax is valid</p>";
        } else {
            echo "<p class='error'>❌ module.php syntax errors: " . htmlspecialchars($output) . "</p>";
        }
        
        // Check if class name appears in file
        $module_content = file_get_contents(__DIR__ . '/module.php');
        if (strpos($module_content, 'class NamelessSentry_Module') !== false) {
            echo "<p class='success'>✅ NamelessSentry_Module class is defined</p>";
        } else {
            echo "<p class='error'>❌ NamelessSentry_Module class not found in module.php</p>";
        }
        
        if (strpos($module_content, 'extends Module') !== false) {
            echo "<p class='success'>✅ Class extends Module</p>";
        } else {
            echo "<p class='error'>❌ Class doesn't extend Module</p>";
        }
        
    } else {
        echo "<p class='error'>❌ module.php file not found</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Exception checking module: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Check Composer autoload
echo "<h2>Composer Dependencies</h2>";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    try {
        require_once(__DIR__ . '/vendor/autoload.php');
        echo "<p class='success'>✅ Composer autoload works</p>";
        
        // Check if Sentry is available
        if (function_exists('Sentry\\init')) {
            echo "<p class='success'>✅ Sentry SDK is available (function)</p>";
        } elseif (class_exists('Sentry\\ClientInterface')) {
            echo "<p class='success'>✅ Sentry SDK is available (class)</p>";
        } elseif (class_exists('Sentry\\State\\Hub')) {
            echo "<p class='success'>✅ Sentry SDK is available (Hub class)</p>";
        } else {
            echo "<p class='error'>❌ Sentry SDK not found</p>";
            
            // Debug: List what Sentry classes are available
            $sentry_classes = [];
            foreach (get_declared_classes() as $class) {
                if (strpos($class, 'Sentry') === 0) {
                    $sentry_classes[] = $class;
                }
            }
            if ($sentry_classes) {
                echo "<p>Available Sentry classes: " . implode(', ', array_slice($sentry_classes, 0, 5)) . "</p>";
            }
        }
        
        // Check if Monolog is available
        if (class_exists('Monolog\\Logger')) {
            echo "<p class='success'>✅ Monolog is available</p>";
        } else {
            echo "<p class='error'>❌ Monolog not found</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error loading composer autoload: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p class='error'>❌ vendor/autoload.php not found - run 'composer install'</p>";
}

// Check NamelessMC integration
echo "<h2>NamelessMC Integration</h2>";
$nameless_root = dirname(dirname(__DIR__));
echo "<p>Expected NamelessMC root: $nameless_root</p>";

if (file_exists($nameless_root . '/core/init.php')) {
    echo "<p class='success'>✅ NamelessMC core found</p>";
    echo "<p class='warning'>⚠️ NOT loading NamelessMC core to avoid ErrorHandler issue</p>";
} else {
    echo "<p class='error'>❌ NamelessMC core not found at expected location</p>";
}

// Check for recent errors
echo "<h2>Error Checking</h2>";
$error_log_locations = [
    '/var/log/apache2/error.log',
    '/var/log/nginx/error.log', 
    '/var/log/php_errors.log',
    $nameless_root . '/cache/logs/errors.log'
];

foreach ($error_log_locations as $log_file) {
    if (file_exists($log_file) && is_readable($log_file)) {
        echo "<p>Checking: $log_file</p>";
        $lines = file($log_file);
        $recent_lines = array_slice($lines, -10); // Last 10 lines
        
        echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 200px; overflow-y: scroll;'>";
        foreach ($recent_lines as $line) {
            if (stripos($line, 'sentry') !== false || stripos($line, 'nameless') !== false) {
                echo "<strong>" . htmlspecialchars($line) . "</strong>";
            } else {
                echo htmlspecialchars($line);
            }
        }
        echo "</pre>";
        break;
    }
}

echo "<h2>Instructions</h2>";
echo "<p>1. Access this file at: http://yourdomain.com/modules/NamelessSentry/server_debug.php</p>";
echo "<p>2. Fix any red ❌ issues shown above</p>";
echo "<p>3. Make sure composer install was run successfully</p>";
echo "<p>4. Check file permissions (should be 755 for directories, 644 for files)</p>";
?>
