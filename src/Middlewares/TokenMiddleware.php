<?php
namespace App\Middlewares;

use App\Daos\TokenDao;
use App\Libs\JwtTokenClass;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use App\Controllers\Controller;

class TokenMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // checa a sessão antes de processar o controller, se não autenticado retorna o response com um header de redirecionamento
        $token = JwtTokenClass::getBearerToken();
        $tokenDao = new TokenDao();
        if(!empty($token)){
            $data = JwtTokenClass::verifyTokenJWT();
            if ($data){
                $result = $tokenDao->validaToken($token,$data->data->CODUSUARIO);
                if (!$result) {
                    $tokenDao->setTokenVencido($token);
                    return $this->setError();
                }
            } else {
                return $this->setError();
            }
        } else {
            return $this->setError();
        }

        $response = $handler->handle($request);

        return $response;
    }

    private function setError(){
        $data = [
            "ERROR" => true,
            "MESSAGE" => "Token invalido",
            "CODE" => "4000"
        ];
        $response = new Response();
        $response->getBody()->write( json_encode($data));
        return $response->withStatus(200)->withHeader('content-type', 'application/json');
    }
}