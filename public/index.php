<?php
require dirname(__DIR__,1) . '/vendor/autoload.php';
require dirname(__DIR__, 1) . '/config/config.php';

use App\Routes\Route;
use Slim\Psr7\Response;

if (CONFIG_MAINTENANCE){
    $data = [
        "error" => true,
        "message" => "Serviço em manutenção",
    ];
    $response = new Response();
    $response->getBody()->write( json_encode($data));
    $response->withStatus(200)->withHeader('content-type', 'application/json');
    die();
}

if (CONFIG_DISPLAY_ERROR_DETAILS){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

new Route();






