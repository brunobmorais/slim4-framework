<?php
namespace App\Routes;


use DI\Container;
use DI\ContainerBuilder;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Throwable;


class Route
{

    function __construct(){

        try {
            $containerBuilder = new ContainerBuilder();
            $container = $containerBuilder->build();
            $container->set('upload_directory', __DIR__ . '/uploads');

        } catch (\Exception $e) {
        }
        AppFactory::setContainer($container);
        $app = AppFactory::create();
        $app->addBodyParsingMiddleware();

        // LIBERAR MÉTODOS
        $app->add(function (Request $request, RequestHandlerInterface $handler): \Psr\Http\Message\ResponseInterface {
            $routeContext = RouteContext::fromRequest($request);
            $routingResults = $routeContext->getRoutingResults();
            $methods = $routingResults->getAllowedMethods();
            $requestHeaders = $request->getHeaderLine('Access-Control-Request-Headers');

            $response = $handler->handle($request);

            $response = $response->withHeader('Access-Control-Allow-Origin', '*');
            $response = $response->withHeader('Access-Control-Allow-Methods', implode(',', $methods));
            $response = $response->withHeader('Access-Control-Allow-Headers', $requestHeaders);

            // Optional: Allow Ajax CORS requests with Authorization header
            // $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');

            return $response;
        });

        $app->addRoutingMiddleware();

        // MIDDLEWARE MANUTENÇÃO
        $app->add(function (Request $request, RequestHandler $handler) {
            $response = $handler->handle($request);
            $existingContent = (string) $response->getBody();

            if (CONFIG_MAINTENANCE){
                $data = [
                    "error" => true,
                    "message" => "Serviço em manutenção",
                ];
                $response = new Response();
                $response->getBody()->write( json_encode($data));
                $response->withStatus(200)->withHeader('content-type', 'application/json');
                return $response;
            }
            return $response;
        });


        // TELA INICIAL
        $app->get('/', function (Request $request, Response $response, array $args) {
            $data = ["ERROR" => false, "MESSAGE" => CONFIG_SITE['name'],];
            $response->getBody()->write(json_encode($data,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            return $response->withStatus(200)->withHeader('Content-type', 'application/json');

        });

        // GRUPO
        $app->group('/v1' , function ( RouteCollectorProxy $group){
            new V1Route($group);
        });

        // DISPLAY ERROR
        if (CONFIG_DISPLAY_ERROR_DETAILS)
            $errorMiddleware = $app->addErrorMiddleware(true, true, true);
        else
            $errorMiddleware = $app->addErrorMiddleware(false, false, false);


        // ERROS
        $errorMiddleware->setDefaultErrorHandler(function (
            Request $request,
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

        // EXECUTA
        $app->run();
    }

}