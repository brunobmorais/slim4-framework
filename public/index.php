<?php
require dirname(__DIR__,1) . '/vendor/autoload.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Routes\Route;

if (CONFIG_DISPLAY_ERROR_DETAILS){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

new Route();






