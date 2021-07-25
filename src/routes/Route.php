<?php
namespace src\routes;


use DI\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;
use src\routes\v1\RouteV1;
use Throwable;

class Route
{

    function __construct(){

        $container = new Container();
        AppFactory::setContainer($container);
        $app = AppFactory::create();
        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();

        $app->group('/v1' , function ( RouteCollectorProxy $group){
            new RouteV1($group);
        });

        $app->get('/clientes[/{id}]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
            // responde a ambos os endereços '/clientes' e `/clientes/42'
            // mas não responde a  '/clientes/'

            return $response;
        });

        $errorMiddleware = $app->addErrorMiddleware(true, true, true);

        // Set the Not Found Handler
        $errorMiddleware->setErrorHandler(
            HttpNotFoundException::class,
            function (ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails) {
                $response = new Response();
                $data = ["error" => true, "mesage" => 'Nenhuma rota encontrada',];
                $response->getBody()->write( json_encode($data));

                return $response->withStatus(404);
            });

        $app->run();
    }

}