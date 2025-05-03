<?php
// Configuration générale de l'application

// Informations sur l'application
define('APP_NAME', 'FieldSync');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost:81/fieldsync');

// Configuration des emails
define('MAIL_HOST', 'smtp.example.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'notifications@fieldsync.com');
define('MAIL_PASSWORD', 'your_password');
define('MAIL_FROM_ADDRESS', 'notifications@fieldsync.com');
define('MAIL_FROM_NAME', 'FieldSync Notifications');

// Configuration des SMS (exemple avec Twilio)
define('SMS_PROVIDER', 'twilio'); // Options: 'twilio', 'nexmo', etc.
define('SMS_ACCOUNT_SID', 'your_account_sid');
define('SMS_AUTH_TOKEN', 'your_auth_token');
define('SMS_FROM_NUMBER', '+1234567890');

// Fuseaux horaires
date_default_timezone_set('Africa/Ouagadougou');

// Options de debug
define('DEBUG_MODE', true);
if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
?>