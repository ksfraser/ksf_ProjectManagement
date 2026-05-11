<?php
/**
 * PHPUnit Bootstrap
 *
 * Sets up autoloading and test environment
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

if (!defined('TB_PREF')) {
    define('TB_PREF', 'fa_');
}
if (!defined('PROJECT_MANAGEMENT_TABLE_PREFIX')) {
    define('PROJECT_MANAGEMENT_TABLE_PREFIX', 'fa_pm_');
}