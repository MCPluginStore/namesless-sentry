#!/bin/bash
# Installation script for NamelessSentry Module

echo "NamelessSentry Module Installation Guide"
echo "======================================="
echo ""

echo "Current directory structure check:"
echo "- Current directory: $(pwd)"
echo "- Files in directory:"
ls -la

echo ""
echo "STEP-BY-STEP INSTALLATION:"
echo ""

echo "1. Make sure you're in the NamelessMC modules directory"
echo "   Expected path: /path/to/namelessmc/modules/NamelessSentry/"
echo ""

echo "2. Run composer install to download dependencies:"
echo "   composer install"
echo ""

echo "3. Check that vendor directory was created:"
echo "   ls -la vendor/"
echo ""

echo "4. The module directory should contain:"
echo "   - init.php"
echo "   - module.php" 
echo "   - module.json"
echo "   - SentryIntegration.php"
echo "   - composer.json"
echo "   - vendor/ (after composer install)"
echo "   - pages/"
echo "   - templates/"
echo ""

echo "5. Enable the module in NamelessMC admin panel"
echo "   Go to: Admin Panel > Modules > Enable 'NamelessSentry'"
echo ""

echo "If you get errors, check:"
echo "- PHP error logs"
echo "- NamelessMC cache/logs directory"
echo "- Browser developer console (F12)"
