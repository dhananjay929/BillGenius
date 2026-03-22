<?php
/**
 * Smart Biller - Application Bootstrap
 * Require this once at the top of any entry point.
 */

define('BASE_PATH', __DIR__);
define('DOWNLOAD_PATH', BASE_PATH . '/download/');
define('DOWNLOAD_WEB_PATH', 'download/');

date_default_timezone_set('Asia/Kolkata');

// Core helpers (replaces electricity_bill_function.php)
require_once BASE_PATH . '/app/Helpers/functions.php';

// Controllers
require_once BASE_PATH . '/app/Controllers/BillController.php';

// Ensure download directory exists
if (!is_dir(DOWNLOAD_PATH)) {
    mkdir(DOWNLOAD_PATH, 0755, true);
}
