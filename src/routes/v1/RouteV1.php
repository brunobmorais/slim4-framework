<?php

namespace src\routes\v1;


use App\Controllers\Intranet\ArquivoController;
use App\Controllers\Intranet\AutUserController;
use App\Controllers\Intranet\DareController;
use App\Controllers\Intranet\DArquivoDigitalController;
use App\Controllers\Intranet\DGrupoController;
use App\Controllers\Intranet\DProfissionalController;

use App\Controllers\Intranet\DProjetoController;
use App\Controllers\Intranet\DTipoArquivoDigitalController;
use App\Controllers\Intranet\FuncionarioController;
use App\Middlewares\VerificaTokenIntranet;
use App\Middlewares\VerificaTokenPrevenir;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Routing\RouteCollectorProxy;
use src\controllers\TesteController;
use src\middlewares\JwtMiddleware;

class RouteV1
{
    function __construct(RouteCollectorProxy $group)
    {
        $group->get('/hello' , TesteController::class.':hello')
            ->add(new JwtMiddleware());
    }
}