# NamelessSentry Module - Configuration

## Setup Instructions

1. **Upload module to server:**
   ```
   /var/www/test/modules/NamelessSentry/
   ```

2. **Install dependencies:**
   ```bash
   cd /var/www/test/modules/NamelessSentry
   composer install
   ```

3. **Create configuration file:**
   ```bash
   cp config.example.php config.php
   ```

4. **Edit config.php with your Sentry settings:**
   ```php
   <?php
   return [
       'dsn' => 'https://your-sentry-dsn@sentry.io/project',
       'environment' => 'production',
       'enable_frontend' => true,
       'enable_replay' => true,
       'enable_feedback' => true,
       'traces_sample_rate' => 0.1,
       'replays_sample_rate' => 0.1,
   ];
   ```

5. **Enable module in NamelessMC admin panel**

## Configuration Options

- **dsn**: Your Sentry project DSN (required)
- **environment**: Environment name (production, staging, development)
- **enable_frontend**: Enable JavaScript error tracking
- **enable_replay**: Enable session replay recording
- **enable_feedback**: Enable user feedback widget
- **traces_sample_rate**: Performance monitoring sample rate (0.0 to 1.0)
- **replays_sample_rate**: Session replay sample rate (0.0 to 1.0)
- **release**: Optional release version

## Usage

The module will automatically:
- Initialize Sentry for PHP error tracking
- Set up Monolog integration for structured logging
- Inject JavaScript tracking code (if frontend enabled)
- Filter logs to only send ERROR level and above to Sentry

## Logging Examples

```php
$logger = \SentryIntegration\SentryIntegration::getLogger();
$logger->error('Database connection failed', ['database' => 'users']);
$logger->warning('High memory usage detected', ['memory_mb' => 512]);
```

## Troubleshooting

- Module not appearing: Check file permissions and directory name
- 500 errors: Check Apache error logs and ensure config.php exists
- No errors in Sentry: Verify DSN and ensure ERROR level logs are generated
