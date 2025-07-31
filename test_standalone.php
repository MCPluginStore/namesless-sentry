<?php
// Simple test to see if module can be loaded without NamelessMC
// Place this in the module directory and run it

echo "Testing NamelessSentry module loading...\n\n";

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

// Test 2: Try to load module class
echo "\n2. Module class loading:\n";
try {
    require_once('module.php');
    echo "   ✅ module.php loaded\n";
    
    if (class_exists('NamelessSentry_Module')) {
        echo "   ✅ NamelessSentry_Module class found\n";
    } else {
        echo "   ❌ NamelessSentry_Module class not found\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "   ❌ Error loading module: " . $e->getMessage() . "\n";
    exit(1);
} catch (Error $e) {
    echo "   ❌ Fatal error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Check module.json
echo "\n3. Module configuration:\n";
$config = json_decode(file_get_contents('module.json'), true);
if ($config) {
    echo "   ✅ module.json is valid JSON\n";
    echo "   Module name: " . $config['name'] . "\n";
    echo "   Version: " . $config['module_version'] . "\n";
} else {
    echo "   ❌ module.json is invalid\n";
    exit(1);
}

echo "\n✅ ALL TESTS PASSED - Module should work!\n";
echo "\nNext steps:\n";
echo "1. Upload this directory to /var/www/test/modules/NamelessSentry/\n";
echo "2. Run 'composer install' in that directory\n";
echo "3. Enable module in NamelessMC admin panel\n";
?>
