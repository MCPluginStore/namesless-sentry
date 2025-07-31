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
            ],
            'sentry_environment' => [
                Validate::MAX => 50
            ]
        ]);

        if ($validation->passed()) {
            try {
                $db = DB::getInstance();
                
                // Update or insert settings
                $settings = [
                    'sentry_dsn' => Input::get('sentry_dsn'),
                    'sentry_environment' => Input::get('sentry_environment') ?: 'production',
                    'sentry_enable_frontend' => Input::get('enable_frontend') == '1' ? '1' : '0',
                    'sentry_enable_replay' => Input::get('enable_replay') == '1' ? '1' : '0',
                    'sentry_enable_feedback' => Input::get('enable_feedback') == '1' ? '1' : '0',
                    'sentry_traces_sample_rate' => (float)Input::get('traces_sample_rate') ?: 0.1,
                    'sentry_replays_sample_rate' => (float)Input::get('replays_sample_rate') ?: 0.1
                ];

                foreach ($settings as $name => $value) {
                    // Check if setting exists
                    $existing = $db->get('settings', ['name', '=', $name]);
                    if ($existing->count()) {
                        // Update existing setting
                        $db->update('settings', $existing->first()->id, [
                            'value' => $value
                        ]);
                    } else {
                        // Insert new setting
                        $db->insert('settings', [
                            'name' => $name,
                            'value' => $value
                        ]);
                    }
                }

                $success = 'Sentry settings updated successfully!';
            } catch (Exception $e) {
                $errors = ['Failed to save settings: ' . $e->getMessage()];
            }
        } else {
            $errors = $validation->errors();
        }
    } else {
        $errors = [$language->get('general', 'invalid_token')];
    }
}

// Get current settings
try {
    $db = DB::getInstance();
    
    $current_dsn = '';
    $current_environment = 'production';
    $enable_frontend = true;
    $enable_replay = true;
    $enable_feedback = true;
    $traces_sample_rate = 0.1;
    $replays_sample_rate = 0.1;

    // Load existing settings
    $sentry_settings = $db->query("SELECT `name`, `value` FROM nl2_settings WHERE `name` LIKE 'sentry_%'")->results();
    
    foreach ($sentry_settings as $setting) {
        switch ($setting->name) {
            case 'sentry_dsn':
                $current_dsn = $setting->value;
                break;
            case 'sentry_environment':
                $current_environment = $setting->value;
                break;
            case 'sentry_enable_frontend':
                $enable_frontend = (bool)$setting->value;
                break;
            case 'sentry_enable_replay':
                $enable_replay = (bool)$setting->value;
                break;
            case 'sentry_enable_feedback':
                $enable_feedback = (bool)$setting->value;
                break;
            case 'sentry_traces_sample_rate':
                $traces_sample_rate = (float)$setting->value;
                break;
            case 'sentry_replays_sample_rate':
                $replays_sample_rate = (float)$setting->value;
                break;
        }
    }
} catch (Exception $e) {
    $errors = ['Failed to load settings: ' . $e->getMessage()];
}

$smarty->assign([
    'PARENT_PAGE' => PARENT_PAGE,
    'DASHBOARD' => $language->get('admin', 'dashboard'),
    'MODULES' => $language->get('admin', 'modules'),
    'PAGE' => PANEL_PAGE,
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit'),
    
    // Page content
    'SENTRY_SETTINGS' => 'Sentry Integration Settings',
    'SENTRY_DSN' => 'Sentry DSN',
    'SENTRY_DSN_VALUE' => Output::getClean($current_dsn),
    'SENTRY_DSN_HELP' => 'Get this from your Sentry project settings (e.g., https://abc123@o0.ingest.sentry.io/12345)',
    
    'SENTRY_ENVIRONMENT' => 'Environment',
    'SENTRY_ENVIRONMENT_VALUE' => Output::getClean($current_environment),
    'SENTRY_ENVIRONMENT_HELP' => 'Environment name (production, staging, development)',
    
    'ENABLE_FRONTEND' => 'Enable Frontend Integration',
    'ENABLE_FRONTEND_VALUE' => $enable_frontend,
    'ENABLE_FRONTEND_HELP' => 'Enable JavaScript error tracking and user feedback widget',
    
    'ENABLE_REPLAY' => 'Enable Session Replay',
    'ENABLE_REPLAY_VALUE' => $enable_replay,
    'ENABLE_REPLAY_HELP' => 'Record user sessions for debugging (requires Frontend Integration)',
    
    'ENABLE_FEEDBACK' => 'Enable User Feedback Widget',
    'ENABLE_FEEDBACK_VALUE' => $enable_feedback,
    'ENABLE_FEEDBACK_HELP' => 'Show feedback widget for users to report issues',
    
    'TRACES_SAMPLE_RATE' => 'Performance Monitoring Sample Rate',
    'TRACES_SAMPLE_RATE_VALUE' => $traces_sample_rate,
    'TRACES_SAMPLE_RATE_HELP' => 'Percentage of transactions to monitor (0.0 to 1.0)',
    
    'REPLAYS_SAMPLE_RATE' => 'Session Replay Sample Rate', 
    'REPLAYS_SAMPLE_RATE_VALUE' => $replays_sample_rate,
    'REPLAYS_SAMPLE_RATE_HELP' => 'Percentage of sessions to record (0.0 to 1.0)',
    
    'TEST_CONNECTION' => 'Test Connection'
]);

if (isset($success)) {
    $smarty->assign('SUCCESS', $success);
}

if (isset($errors)) {
    $smarty->assign('ERRORS', $errors);
}

$smarty->display('custom/templates/SentryIntegration/sentry_settings.tpl');
