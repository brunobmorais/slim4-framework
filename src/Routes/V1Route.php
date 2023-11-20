<?php

namespace App\Routes;

use App\Controllers\UsuarioController;
use App\Middlewares\TokenMiddleware;
use Slim\Routing\RouteCollectorProxy;

class V1Route
{
    function __construct(RouteCollectorProxy $app)
    {

        // ROTAS DO USUARIO
        $app->group('/usuario' , function (RouteCollectorProxy $group){
            $group->post('' ,UsuarioController::class.':insertUsuario');
            $group->put('' , UsuarioController::class.':updateUsuario')
                ->add(new TokenMiddleware());
            $group->get('/{codusuario}' , UsuarioController::class.':selectUsuario')
                ->add(new TokenMiddleware());
            $group->post('/datasession' , UsuarioController::class.':informacoesUsuario')
                ->add(new TokenMiddleware());
            $group->post('/novasenha' ,UsuarioController::class.':insertNovaSenha');
            $group->post('/login' , UsuarioController::class.':login');
            $group->put('/logoff' , UsuarioController::class.':logoff')
                ->add(new TokenMiddleware());
        });

        // VERIFICAÇÃO DE TOKEN
        $app->post('/newtoken' , UsuarioController::class.':insertNewToken')
            ->add(new TokenMiddleware());
        // VERIFICA SESSAO
        $app->post('/session' , UsuarioController::class.':sessao')
            ->add(new TokenMiddleware());


        // BUSCAR DISPOSITIVOS DO USUÁRIO
        $app->get('/execute' ,UsuarioController::class.':execute');

    }
}