<?php
/**
 * Smart Biller - Application Configuration
 */

define('APP_NAME', 'Smart Biller');
define('APP_VERSION', '2.0');
define('BASE_PATH', dirname(__DIR__));
define('DOWNLOAD_PATH', BASE_PATH . '/download/');
define('DOWNLOAD_WEB_PATH', './download/');

// Captcha (optional)
define('CAPTCHA_API_KEY', 'e72acf92e1a5b092e9e7a5e319beb0af');

// Timezone
date_default_timezone_set('Asia/Kolkata');
