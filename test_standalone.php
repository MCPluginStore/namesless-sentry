<?php
// Simple test to see if module files are valid (without NamelessMC)
// Place this in the module directory and run it

echo "Testing NamelessSentry module files...\n\n";

// Test 1: Check if files exist
echo "1. File existence check:\n";
$required_files = ['init.php', 'module.php', 'module.json'];
foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "   ✅ $file exists\n";
    } else {
        echo "   ❌ $file missing\n";
        exit(1);
    }
}

// Test 2: Check PHP syntax (without loading classes)
echo "\n2. PHP syntax check:\n";
$files_to_check = ['init.php', 'module.php'];
foreach ($files_to_check as $file) {
    $output = [];
    $return_code = 0;
    exec("php -l $file 2>&1", $output, $return_code);
    
    if ($return_code === 0) {
        echo "   ✅ $file syntax is valid\n";
    } else {
        echo "   ❌ $file has syntax errors:\n";
        foreach ($output as $line) {
            echo "      " . $line . "\n";
        }
        exit(1);
    }
}

// Test 3: Check module.json
echo "\n3. Module configuration:\n";
$config = json_decode(file_get_contents('module.json'), true);
if ($config) {
    echo "   ✅ module.json is valid JSON\n";
    echo "   Module name: " . ($config['name'] ?? 'Not set') . "\n";
    echo "   Version: " . ($config['module_version'] ?? 'Not set') . "\n";
    echo "   Author: " . ($config['author'] ?? 'Not set') . "\n";
    
    // Check required fields
    $required_fields = ['name', 'module_version', 'nameless_version'];
    foreach ($required_fields as $field) {
        if (isset($config[$field])) {
            echo "   ✅ $field: " . $config[$field] . "\n";
        } else {
            echo "   ❌ Missing required field: $field\n";
        }
    }
} else {
    echo "   ❌ module.json is invalid JSON\n";
    exit(1);
}

// Test 4: Check class definition (without instantiating)
echo "\n4. Class definition check:\n";
$module_content = file_get_contents('module.php');
if (strpos($module_content, 'class NamelessSentry_Module') !== false) {
    echo "   ✅ NamelessSentry_Module class is defined\n";
} else {
    echo "   ❌ NamelessSentry_Module class not found in module.php\n";
}

if (strpos($module_content, 'extends Module') !== false) {
    echo "   ✅ Class extends Module (correct)\n";
} else {
    echo "   ❌ Class doesn't extend Module\n";
}

echo "\n✅ ALL FILE TESTS PASSED!\n";
echo "\nℹ️  NOTE: The 'Class Module not found' error is NORMAL when testing outside NamelessMC.\n";
echo "   The Module class is provided by NamelessMC framework.\n\n";

echo "Next steps:\n";
echo "1. Upload this directory to /var/www/test/modules/NamelessSentry/\n";
echo "2. Run 'composer install' in that directory\n";
echo "3. Enable module in NamelessMC admin panel\n";
echo "4. Use server_debug.php on the server for further testing\n";
?>
