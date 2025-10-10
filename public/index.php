<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
$baseDir = __DIR__;
if (!file_exists($baseDir.'/vendor/autoload.php') || !file_exists($baseDir.'/bootstrap/app.php')) {
    $baseDir = dirname(__DIR__);
}
require $baseDir.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $baseDir.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
