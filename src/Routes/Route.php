<?php
namespace App\Routes;


use DI\Container;
use DI\ContainerBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;
use Throwable;


class Route
{

    function __construct(){

        $containerBuilder = new ContainerBuilder();
        $container = $containerBuilder->build();
        $container->set('upload_directory', __DIR__ . '/uploads');
        AppFactory::setContainer($container);
        $app = AppFactory::create();
        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();

        $app->get('/', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
            $data = ["ERROR" => true, "MESSAGE" => CONFIG_SITE['name'],];
            $response->getBody()->write(json_encode($data));
            $response->withHeader('Content-Type', 'application/json');
            return $response;
        });

        $app->group('/v1' , function ( RouteCollectorProxy $group){
            new V1Route($group);
        });

        if (CONFIG_DISPLAY_ERROR_DETAILS)
            $errorMiddleware = $app->addErrorMiddleware(true, true, true);
        else
            $errorMiddleware = $app->addErrorMiddleware(false, false, false);



        $errorMiddleware->setDefaultErrorHandler(function (
            ServerRequestInterface $request,
            Throwable $exception,
            bool $displayErrorDetails,
            bool $logErrors,
            bool $logErrorDetails,
            ?LoggerInterface $logger = null
        ) use ($app) {
            $erro = [];
            if (CONFIG_DISPLAY_ERROR_DETAILS){
                if (!empty($exception)) {
                    $erro = array(
                        "CODE" => $exception->getCode(),
                        "MESSAGE" => $exception->getMessage(),
                        "FILE" => $exception->getFile(),
                        "LINE" => $exception->getLine()
                    );
                }
            }
            $payload = ["ERROR" => true, "MESSAGE" => "Erro ao acessar a rota", "EXCEPTION" => $erro];

            $response = $app->getResponseFactory()->createResponse();
            $response->getBody()->write(json_encode($payload,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            return $response->withStatus(200)->withHeader('Content-type', 'application/json');
        });

        $app->run();
    }

}