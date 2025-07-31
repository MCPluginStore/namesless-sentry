@echo off
echo NamelessSentry Module Setup for Windows
echo ========================================
echo.

echo Checking current directory...
echo Current directory: %CD%
echo.

echo Files in current directory:
dir /b
echo.

echo INSTALLATION STEPS:
echo.
echo 1. Install Composer (if not installed):
echo    - Download from: https://getcomposer.org/download/
echo    - Run the installer
echo.

echo 2. Run composer install:
echo    composer install
echo.

echo 3. Check if vendor directory exists:
if exist "vendor" (
    echo    ✅ vendor directory found
) else (
    echo    ❌ vendor directory missing - run 'composer install'
)
echo.

echo 4. Module directory structure:
echo    Required files:
if exist "init.php" (echo    ✅ init.php) else (echo    ❌ init.php missing)
if exist "module.php" (echo    ✅ module.php) else (echo    ❌ module.php missing)
if exist "module.json" (echo    ✅ module.json) else (echo    ❌ module.json missing)
if exist "composer.json" (echo    ✅ composer.json) else (echo    ❌ composer.json missing)
if exist "SentryIntegration.php" (echo    ✅ SentryIntegration.php) else (echo    ❌ SentryIntegration.php missing)
echo.

echo 5. Directory name should be 'NamelessSentry' (not 'namesless-sentry')
echo    Move this directory to: NamelessMC/modules/NamelessSentry/
echo.

echo 6. Enable module in NamelessMC admin panel
echo.

pause
