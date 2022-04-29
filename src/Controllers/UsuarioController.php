<?php

namespace App\Controllers;

use App\Daos\TokenDao;
use App\Daos\UsuarioDao;
use App\Libs\JwtTokenClass;
use App\Libs\UuidClass;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


final class UsuarioController extends Controller
{

    public function login(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $usuarioDao = new UsuarioDao();
        $dataJson = $request->getParsedBody();

        $obj = $usuarioDao->loginUsuario($dataJson);

        return $this->withJson($response, $obj);
    }

    public function logoff(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $tokenDao = new TokenDao();
        $dataJson = $request->getParsedBody();
        $token = $dataJson['TOKEN'];
        $refreshtoken = $dataJson['REFRESHTOKEN'];

        $tokenDao->setTokenVencido($token);
        $tokenDao->setTokenVencido($refreshtoken);

        $data = [
            "ERROR" => false,
            "MESSAGE" => "Efetuado com sucesso",
            "CODE" => "2000"
        ];
        return $this->withJson($response, $data);;
    }

    public function selectUsuario(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $codusuario = $args['codusuario'];
        $usuarioDao = new UsuarioDao();

        if (!empty($codusuario))
            $obj = $usuarioDao->buscarInformacoesUsuario($codusuario);
        else {
            $obj = [
                "ERROR" => true,
                "MESSAGE" => "Parametro(s) não enviado",
                "CODE" => "4001"
            ];
        }

        return $this->withJson($response, $obj);
    }

    public function insertUsuario(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $usuarioDao = new UsuarioDao();

        $dataJson = $request->getParsedBody();

        $obj = $usuarioDao->insertUsuario($dataJson);

        return $this->withJson($response, $obj);
    }

    public function insertNovaSenha(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $usuarioDao = new UsuarioDao();

        $dataJson = $request->getParsedBody();

        $obj = $usuarioDao->insertNovaSenha($dataJson);
        return $this->withJson($response, $obj);
    }

    public function sessao(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $data = [
            "ERROR" => false,
            "MESSAGE" => "Sessão ativa",
            "CODE" => "2000"
        ];
        return $this->withJson($response, $data);
    }

    public function insertNewToken(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $tokenDao = new TokenDao();
        $jwt = new JwtTokenClass();

        $dataJson = $request->getParsedBody();

        $codusuario = $jwt->decode(JwtTokenClass::getBearerToken())->data->CODUSUARIO;

        if (!empty($codusuario)) {
            $data = [
                "ERROR" => false,
                "MESSAGE" => "Token gerado",
                "CODE" => "2000",
                "TOKEN" => $tokenDao->inserirAccessToken($codusuario),
            ];
        } else {
            $data = [
                "ERROR" => true,
                "MESSAGE" => "Parametro(s) não enviado",
                "CODE" => "4001"
            ];
        }
        return $this->withJson($response, $data);
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {


        return $this->withJson($response, $obj);
    }

}