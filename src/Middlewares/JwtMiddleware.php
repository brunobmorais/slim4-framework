<?php


namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use src\Controllers\Controller;

class JwtMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // checa a sessão antes de processar o controller, se não autenticado retorna o response com um header de redirecionamento
        if( !isset($_SESSION['userAuth']) ){
            $data = [
                "error" => true,
                "message" => "Token invalido",
            ];
            $response = new Response();
            $response->getBody()->write( json_encode($data));
            return $response->withStatus(401)->withHeader('content-type', 'application/json');
        }

        $response = $handler->handle($request);

        return $response;
    }
}