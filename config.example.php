<?php
/**
 * Sentry Configuration
 * Copy this file and rename it to config.php, then edit the values below.
 */

return [
    // Required: Your Sentry DSN
    'dsn' => '',
    
    // Optional: Environment (production, staging, development)
    'environment' => 'production',
    
    // Optional: Enable/disable features
    'enable_frontend' => true,
    'enable_replay' => true,
    'enable_feedback' => true,
    
    // Optional: Sample rates (0.0 to 1.0)
    'traces_sample_rate' => 0.1,
    'replays_sample_rate' => 0.1,
    
    // Optional: Release version (leave empty for auto-detection)
    'release' => '',
];
