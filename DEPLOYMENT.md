# Deployment Instructions for NamelessSentry Module

## Your Setup:
- **Development PC (Windows):** `c:\Users\Enno\git\namesless-sentry`
- **Server (Linux):** `/var/www/test` (NamelessMC installation)
- **Composer:** Available on the server, NOT on Windows PC

## Deployment Steps:

### 1. Upload module files to server
Upload your entire `namesless-sentry` directory to the server at:
```
/var/www/test/modules/NamelessSentry/
```

**IMPORTANT:** Rename the directory from `namesless-sentry` to `NamelessSentry` during upload.

### 2. SSH into your server and run composer
```bash
# SSH into your server
ssh your-username@your-server

# Navigate to the module directory
cd /var/www/test/modules/NamelessSentry

# Install composer dependencies
composer install

# Check that vendor directory was created
ls -la vendor/
```

### 3. Set proper permissions
```bash
# Make sure web server can read the files
chown -R www-data:www-data /var/www/test/modules/NamelessSentry
chmod -R 755 /var/www/test/modules/NamelessSentry
```

### 4. Enable module in NamelessMC
- Go to your NamelessMC admin panel
- Navigate to Admin Panel > Modules
- Find "NamelessSentry" in the list
- Click "Enable"

## File Structure After Upload:
```
/var/www/test/modules/NamelessSentry/
├── init.php
├── module.php
├── module.json
├── composer.json
├── SentryIntegration.php
├── vendor/ (created by composer install)
├── pages/
│   └── core/
│       └── sentry_settings.php
└── templates/
    └── sentry_settings.tpl
```

## Upload Methods:
- **SCP/SFTP:** Use WinSCP, FileZilla, or similar
- **Git:** Push to repo, then pull on server
- **FTP:** Traditional FTP client

## If you get errors:
1. Check `/var/log/apache2/error.log` (or nginx equivalent)
2. Check `/var/www/test/cache/logs/` for NamelessMC logs
3. Enable PHP error display temporarily in NamelessMC
