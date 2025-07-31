<?php
return [
    // REPLACE THIS with your actual Sentry DSN
    'dsn' => '',
    
    // Environment name
    'environment' => 'production',
    
    // Frontend features (set to false for now to keep it simple)
    'enable_frontend' => false,
    'enable_replay' => false,
    'enable_feedback' => false,
    
    // Sample rates
    'traces_sample_rate' => 0.1,
    'replays_sample_rate' => 0.1,
    
    // Release version (optional)
    'release' => '',
];
?>
