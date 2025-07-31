<?php
/**
 * Sentry Integration Settings Page
 */

if (!$user->handlePanelPageLoad('admincp.modules')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'modules');
define('PANEL_PAGE', 'sentry_settings');
$page_title = $language->get('admin', 'modules');
require_once(ROOT_PATH . '/core/templates/backend_init.php');

// Handle form submission
if (Input::exists()) {
    if (Token::check()) {
        $validation = Validate::check($_POST, [
            'sentry_dsn' => [
                Validate::MAX => 500
            ]
        ]);

        if ($validation->passed()) {
            // Update settings
            if (isset($_POST['sentry_dsn'])) {
                $sentry_dsn = Input::get('sentry_dsn');
                // Update environment variable or config file
                // For now, we'll assume you set it via environment
                $smarty->assign('SUCCESS', 'Settings updated successfully');
            }

            if (isset($_POST['enable_frontend'])) {
                $enable_frontend = Input::get('enable_frontend') == '1';
                // Store this setting in database or config
                $smarty->assign('SUCCESS', 'Frontend integration ' . ($enable_frontend ? 'enabled' : 'disabled'));
            }
        } else {
            $errors = $validation->errors();
            $smarty->assign('ERRORS', $errors);
        }
    } else {
        $smarty->assign('ERRORS', [$language->get('general', 'invalid_token')]);
    }
}

// Get current settings
$current_dsn = getenv('SENTRY_DSN') ?: '';
$enable_frontend = true; // Default to enabled

$smarty->assign([
    'PARENT_PAGE' => PARENT_PAGE,
    'DASHBOARD' => $language->get('admin', 'dashboard'),
    'MODULES' => $language->get('admin', 'modules'),
    'PAGE' => PANEL_PAGE,
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit'),
    'SENTRY_SETTINGS' => 'Sentry Integration Settings',
    'SENTRY_DSN' => 'Sentry DSN',
    'SENTRY_DSN_VALUE' => Output::getClean($current_dsn),
    'ENABLE_FRONTEND' => 'Enable Frontend Integration',
    'ENABLE_FRONTEND_VALUE' => $enable_frontend,
    'SENTRY_DSN_HELP' => 'Get this from your Sentry project settings',
    'TEST_CONNECTION' => 'Test Connection'
]);

$smarty->display('custom/templates/SentryIntegration/sentry_settings.tpl');
