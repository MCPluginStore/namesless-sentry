<?php
// Simple test file that doesn't try to load NamelessMC core
// Access this at: http://yourdomain.com/modules/NamelessSentry/simple_test.php

echo "<h1>Simple Module Test (No NamelessMC)</h1>";

echo "<h2>Basic PHP Test</h2>";
echo "✅ PHP Version: " . PHP_VERSION . "<br>";

echo "<h2>File Test</h2>";
$files = ['init.php', 'module.php', 'module.json', 'composer.json'];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

echo "<h2>Composer Test</h2>";
if (file_exists('vendor/autoload.php')) {
    echo "✅ Vendor directory exists<br>";
    try {
        require_once('vendor/autoload.php');
        echo "✅ Composer autoload works<br>";
        
        if (function_exists('Sentry\\init')) {
            echo "✅ Sentry SDK available<br>";
        } else {
            echo "❌ Sentry SDK not available<br>";
        }
        
        if (class_exists('Monolog\\Logger')) {
            echo "✅ Monolog available<br>";
        } else {
            echo "❌ Monolog not available<br>";
        }
    } catch (Exception $e) {
        echo "❌ Composer error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Vendor directory missing<br>";
}

echo "<h2>Config Test</h2>";
if (file_exists('config.php')) {
    echo "✅ config.php exists<br>";
    try {
        $config = require('config.php');
        if (is_array($config)) {
            echo "✅ Config loads as array<br>";
            if (!empty($config['dsn'])) {
                echo "✅ DSN configured<br>";
            } else {
                echo "⚠️ DSN not configured<br>";
            }
        } else {
            echo "❌ Config is not an array<br>";
        }
    } catch (Exception $e) {
        echo "❌ Config error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "⚠️ config.php missing (create from config.example.php)<br>";
}

echo "<h2>Module Syntax Test</h2>";
$output = shell_exec("php -l module.php 2>&1");
if (strpos($output, 'No syntax errors') !== false) {
    echo "✅ module.php syntax is valid<br>";
} else {
    echo "❌ module.php syntax error: " . htmlspecialchars($output) . "<br>";
}

echo "<h2>Next Steps</h2>";
echo "<p>If all tests above pass, the module files are ready.</p>";
echo "<p>Try enabling the module in NamelessMC admin panel.</p>";
echo "<p>If you get 500 errors, check:</p>";
echo "<ul>";
echo "<li>Apache error log: /var/log/apache2/error.log</li>";
echo "<li>NamelessMC logs in cache/logs/</li>";
echo "<li>Make sure config.php has a valid Sentry DSN</li>";
echo "</ul>";
?>
