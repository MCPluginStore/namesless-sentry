# Sentry Integration Module for NamelessMC

A comprehensive Sentry error tracking and monitoring integration for NamelessMC that provides both backend PHP error tracking and frontend JavaScript error monitoring with session replay and user feedback.

## Features

- 🐛 **PHP Error Tracking**: Automatically captures exceptions and errors
- 🌐 **JavaScript Error Monitoring**: Frontend error tracking with session replay
- 💬 **User Feedback**: Allow users to report bugs directly
- 📊 **Performance Monitoring**: Track page load times and API performance
- 🎯 **Smart Filtering**: Only creates issues for ERROR level and above
- 📹 **Session Replay**: Records user interactions for better debugging
- 🔧 **Monolog Integration**: Structured logging with proper level handling

## Installation

### 1. Install Dependencies

First, make sure you have Composer installed, then run:

```bash
composer install
```

This will install:
- `sentry/sentry ^4.0`
- `monolog/monolog ^3.0`

### 2. Install Module

1. Copy the entire `SentryIntegration` folder to your NamelessMC `modules/` directory
2. The structure should look like:
   ```
   modules/
   └── SentryIntegration/
       ├── module.json
       ├── module.php
       ├── init.php
       ├── SentryIntegration.php
       ├── composer.json
       ├── pages/
       ├── templates/
       └── vendor/ (after composer install)
   ```

### 3. Enable Module

1. Go to your NamelessMC Admin Panel
2. Navigate to **Modules**
3. Find **SentryIntegration** and click **Enable**
4. Go to **Module Settings** → **Sentry Integration Settings**
5. Configure your Sentry DSN and other settings

### 4. Configure Settings

In the admin panel settings page, configure:

- **Sentry DSN**: Your project's DSN from Sentry (e.g., `https://abc123@o0.ingest.sentry.io/12345`)
- **Environment**: Environment name (production, staging, development)
- **Frontend Integration**: Enable/disable JavaScript error tracking
- **Session Replay**: Enable/disable session recording
- **User Feedback**: Enable/disable feedback widget
- **Sample Rates**: Configure performance monitoring and replay sampling

### 5. Add Frontend Integration (Optional)

To enable JavaScript error tracking, session replay, and user feedback, add this to your template's header:

```smarty
{* In your main template header (e.g. header.tpl) *}
{if isset($SENTRY_JS)}
    {$SENTRY_JS}
{/if}
```

Or manually call:
```php
<?php
\SentryIntegration\SentryIntegration::renderJavaScriptIntegration();
?>
```

## Usage

### Backend Logging

The module automatically captures all PHP errors and exceptions. You can also use the logging methods:

```php
<?php
use SentryIntegration\SentryIntegration;

// Different log levels
SentryIntegration::logDebug('Debug information', ['context' => 'data']);
SentryIntegration::logInfo('User logged in', ['user_id' => 123]);
SentryIntegration::logWarning('Deprecated function used');
SentryIntegration::logError('Database connection failed');

// Set user context
SentryIntegration::setUserContext([
    'id' => $user->data()->id,
    'username' => $user->data()->username,
    'email' => $user->data()->email
]);

// Add custom tags
SentryIntegration::setTags([
    'module' => 'forum',
    'action' => 'create_post'
]);

// Performance monitoring
$transaction = SentryIntegration::startTransaction('user.login', 'http.request');
// ... do work ...
$transaction->finish();
?>
```

### Frontend Integration

The frontend integration automatically:
- Captures JavaScript errors
- Records session replays (10% of sessions, 100% on errors)
- Provides a user feedback widget
- Sets user context from NamelessMC user data

### User Feedback

Users can report bugs directly through the feedback widget, or you can capture feedback programmatically:

```php
<?php
$eventId = \Sentry\captureException($exception);
SentryIntegration::captureUserFeedback(
    $eventId,
    'John Doe',
    'john@example.com',
    'The page crashed when I tried to submit the form'
);
?>
```

## Configuration

### Sample Rates

The module uses these default sample rates:
- **Session Replay**: 10% of normal sessions
- **Error Replay**: 100% of sessions with errors
- **Performance Monitoring**: 10% of transactions

You can modify these in `SentryIntegration.php`.

### Privacy Settings

By default, session replays capture everything without masking. If you need privacy:

1. Edit the replay integration in `getJavaScriptConfig()`
2. Set masking options:
   ```javascript
   maskAllText: true,        // Mask all text
   blockAllMedia: true,      // Block images/videos
   maskAllInputs: true       // Mask form inputs
   ```

## Troubleshooting

### Module Not Loading

1. Check that `composer install` was run in the module directory
2. Verify the `SENTRY_DSN` environment variable is set
3. Check NamelessMC error logs

### Frontend Integration Not Working

1. Ensure the Sentry JavaScript is included in your template
2. Check browser console for JavaScript errors
3. Verify the DSN project ID is extracted correctly

### Performance Issues

1. Reduce sample rates in the configuration
2. Disable session replay if not needed
3. Use log level filtering to reduce noise

## Support

For issues related to:
- **Sentry SDK**: Check [Sentry Documentation](https://docs.sentry.io/platforms/php/)
- **NamelessMC Integration**: Create an issue in this repository
- **Module Configuration**: Check the admin panel settings

## License

This module is provided as-is for NamelessMC integration purposes.
