<?php
// Chargement du .env
$_env_file = __DIR__ . '/../.env';
if (file_exists($_env_file)) {
    foreach (file($_env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $_line) {
        if (str_starts_with(trim($_line), '#') || !str_contains($_line, '=')) continue;
        [$_k, $_v] = explode('=', $_line, 2);
        $_ENV[trim($_k)] = trim($_v);
    }
}
function env(string $key, $default = null) {
    return $_ENV[$key] ?? $default;
}

// Application
define('APP_NAME',    'FieldSync');
define('APP_VERSION', '1.0.0');
define('APP_URL',     env('APP_URL', 'http://localhost/fieldsync'));

// Email (PHPMailer)
define('MAIL_HOST',         env('MAIL_HOST',         'smtp.gmail.com'));
define('MAIL_PORT',         (int) env('MAIL_PORT',   587));
define('MAIL_USERNAME',     env('MAIL_USERNAME',     ''));
define('MAIL_PASSWORD',     env('MAIL_PASSWORD',     ''));
define('MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS', 'notifications@fieldsync.com'));
define('MAIL_FROM_NAME',    env('MAIL_FROM_NAME',    'FieldSync'));

// SMS
define('SMS_PROVIDER',     env('SMS_PROVIDER',     'textbelt'));
define('SMS_TEXTBELT_KEY', env('SMS_TEXTBELT_KEY', 'textbelt'));
define('SMS_ACCOUNT_SID',          env('SMS_ACCOUNT_SID',          ''));
define('SMS_AUTH_TOKEN',           env('SMS_AUTH_TOKEN',           ''));
define('SMS_FROM_NUMBER',          env('SMS_FROM_NUMBER',          ''));
define('SMS_MESSAGING_SERVICE_SID', env('SMS_MESSAGING_SERVICE_SID', ''));
define('SMS_CRON_KEY',             env('SMS_CRON_KEY',             ''));

// Fuseau horaire
date_default_timezone_set('Africa/Ouagadougou');

// Debug = false en production
define('DEBUG_MODE', env('DEBUG_MODE', 'false') === 'true');
if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
?>