<?php

namespace App\Libs;

use App\Libs\FuncoesClass;
use Twig\Environment;
use Twig\Extension\EscaperExtension;
use Twig\Loader\FilesystemLoader;

class TwigClass
{



    private $viewDirectory = null;
    private $viewFile = null;
    private $viewUrl = null;

    /**
     * @var string
     */
    private $titlePage;

    public function __construct(){

    }

    public function render(string $view, $data = [], $print = true, $cache = false){

        $loader = new FilesystemLoader(dirname(__DIR__, 2) . '/templates');
        $twig = new Environment($loader);
        if ($cache)
            $twig = new Environment($loader, ['cache' => dirname(__DIR__, 2) . '/templates/cache']);

        $retorno = '';

        $varsDefault = [
            "URL" => CONFIG_URL,
        ];

        $data = array_merge($varsDefault,$data);


        $this->setView($view);

        if (file_exists(dirname(__DIR__, 2) . "/templates/{$this->viewDirectory}/{$this->viewDirectory}.css")) {
            $result = "<!--STYLE CONTROLER-->\n";
            $result .= "<style>";
            $result .= file_get_contents(dirname(__DIR__, 2) . "/templates/{$this->viewDirectory}/{$this->viewDirectory}.css");
            $result .= "</style>";
            $retorno = $result;
        }

        if (file_exists(dirname(__DIR__, 2) . "/templates/{$this->viewDirectory}/{$this->viewFile}/{$this->viewFile}.css")) {
            $result = "<!--STYLE VIEW-->\n";
            $result .= "<style>";
            $result .= file_get_contents(dirname(__DIR__, 2) . "/templates/{$this->viewDirectory}/{$this->viewFile}/{$this->viewFile}.css");
            $result .= "</style>";
            $retorno .= $result;
        }

        if ($this->viewFile === "index") {
            if (file_exists(dirname(__DIR__, 2) . "/templates/{$this->viewDirectory}/{$this->viewDirectory}.html.twig")) {
                //include_once dirname(__DIR__, 2) . "/templates/{$this->viewDirectory}/{$this->viewDirectory}.php";
                //$result .= str_replace($keysMap,$vars, file_get_contents(dirname(__DIR__,2)."/templates/{$this->viewDirectory}/{$this->viewDirectory}.php"));
                $retorno .= $twig->render("{$this->viewDirectory}/{$this->viewDirectory}.html.twig",$data);
            }else {
                //include_once dirname(__DIR__, 2) . "/templates/erro/erro.php";
                //$result .= str_replace($keysMap,$vars, file_get_contents(dirname(__DIR__,2)."/templates/erro/erro.php"));
                $retorno .= $twig->render("erro/erro.html.twig",$data);
            }
        } else if (file_exists(dirname(__DIR__, 2) . "/templates/{$view}/{$this->viewFile}.html.twig")) {
            //include_once dirname(__DIR__, 2) . "/templates/{$view}/{$this->viewFile}.php";
            //$result .= str_replace($keysMap,$vars, file_get_contents(dirname(__DIR__,2)."/templates/{$view}/{$this->viewFile}.php"));
            $retorno .= $twig->render("{$view}/{$this->viewFile}.html.twig",$data);
        } else {
            //include_once dirname(__DIR__, 2) . "/templates/erro/erro.php";
            //$result .= str_replace($keysMap,$vars, file_get_contents(dirname(__DIR__,2)."/templates/erro/erro.php"));
            $retorno .= $twig->render("erro/erro.html.twig",$data);

        }

        if ($print)
            echo $retorno;
        else
            return $retorno;
    }



    protected function setView($url)
    {
        $controller = explode("/", $url);

        $this->viewUrl = $url;
        $this->viewDirectory = $controller[0];
        $this->viewFile = $controller[1];
    }


}