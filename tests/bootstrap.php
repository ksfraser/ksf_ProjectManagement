<?php

declare(strict_types=1);

/*
 * Standalone test bootstrap — NO FrontAccounting, NO transport, NO hooks.
 * Pure Composer autoload for the Ksfraser\ProjectManagement namespace.
 * PHP 7.3 compatible.
 */

$autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    fwrite(STDERR, "Run: composer install\n");
    exit(2);
}
require_once $autoload;
