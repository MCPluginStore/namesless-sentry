# 📜 Logs (PHP / Sentry SDK)

Where logs are used in this PHP project, ensure the [Sentry PHP SDK](https://github.com/getsentry/sentry-php) is properly initialized. For structured logging, it's recommended to integrate with a logging library like **Monolog**.

Sentry provides a `Sentry\Monolog\Handler` that automatically forwards log events (`debug`, `info`, `error`, etc.) to Sentry without the need to instrument every call individually.

---

## ⚙️ Configuration

### ✅ Baseline

Make sure Sentry is initialized early in your application (e.g., `bootstrap.php`, `index.php`, or a service provider):

```php
<?php
require 'vendor/autoload.php';

Sentry\init([
    'dsn' => 'https://examplePublicKey@o0.ingest.sentry.io/0',
    'environment' => 'production',
]);
```

---

### 🔌 Logger Integration (Monolog Example)

If you're using Monolog (e.g., in Laravel, Symfony, or standalone):

```php
<?php
use Monolog\Logger;
use Sentry\Monolog\Handler as SentryHandler;
use Sentry\SentrySdk;

$logger = new Logger('sentry');

// Log everything from DEBUG and above to Sentry
$logger->pushHandler(new SentryHandler(SentrySdk::getCurrentHub(), Logger::DEBUG));
```

This will capture:

- `debug()` → `log`
- `info()` → `info`
- `warning()` → `warn`
- `error()` → `error`
- `critical()` → `fatal`

---

## 🧪 Logger Examples

Use standard Monolog logging methods with structured context:

```php
<?php
$logger->debug('Starting database connection', ['database' => 'users']);
$logger->info(sprintf('Cache miss for user: %s', $userId));
$logger->info('Updated profile', ['profileId' => 345]);
$logger->warning('Rate limit reached for endpoint', [
    'endpoint' => '/api/results/',
    'isEnterprise' => false,
]);
$logger->error('Failed to process payment', [
    'orderId' => 'order_123',
    'amount' => 99.99,
]);
$logger->critical('Database connection pool exhausted', [
    'database' => 'users',
    'activeConnections' => 100,
]);
```

For advanced use (e.g., breadcrumbs or manual error capturing), refer to the [Sentry PHP documentation](https://docs.sentry.io/platforms/php/).