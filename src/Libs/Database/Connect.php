<?php
namespace App\Libs\Database;
/**
 * CLASSE BANCO
 *  Esta classe faz conexão com o banco de dados mysql utilizando o pdo
 *
 * @author Bruno Morais <brunomoraisti@gmail.com>
 * @version 1
 * @copyright GPL © 2021, bmorais.com
 * @package php
 * @subpackage class
 * @access private
 */

use App\Libs\EmailClass;
use PDO;
use PDOException;

class Connect
{

    /** @var PDOException */
    private static $error;

    /** @var PDO */
    private static $instance;

    /**
     * Connect constructor.
     */
    final private function __construct()
    {
    }
    /**
     * Connect clone.
     */
    final private function __clone()
    {
    }

    public static function getInstance():?PDO
    {
        if (!isset (self::$instance)) {

            try {
                self::$instance = new PDO(CONFIG_DATA_LAYER["driver"] . ":host=" . CONFIG_DATA_LAYER["host"] . ";dbname=" . CONFIG_DATA_LAYER["dbname"] . ";port=" . CONFIG_DATA_LAYER["port"],
                    CONFIG_DATA_LAYER["username"],
                    CONFIG_DATA_LAYER["passwd"],
                    CONFIG_DATA_LAYER["options"]);
            } catch (PDOException $e) {
                self::setError($e);
            }
        }

        return self::$instance;
    }

    /**
     * @return PDOException|null
     */
    public static function getError(): ?PDOException
    {
        return self::$error;
    }

    public static function setError(PDOException $e, string $sql=''){
        self::$error = $e;
        $message["ARQUIVO"] =  $e->getFile();
        $message["SQL"] = $sql;
        $message["LINHA"] = $e->getLine();
        $message["MENSAGEM"]= $e->getMessage();
        $message["INFORMACOES"]= $e->getMessage() . " / " . $e->getCode() . " / " . $e->getPrevious() . " / " . $e->getTraceAsString();

        if (CONFIG_DATA_LAYER["display_errors_details"]) {
            $obj = [
                "ERROR" => true,
                "MESSAGE" => "Erro no banco de dados",
                "CODE" => "5000",
                "EXCEPTION" => $message
            ];
            echo json_encode($obj);
            die();
        } else {
            EmailClass::sendEmail("Erro no servidor | ".date('d/m/Y H:i'),var_dump($message),array(CONFIG_DEVELOPER['email']));
            $obj = [
                "error" => true,
                "message" => "Erro no banco de dados",
                "code" => "5000",
            ];        }
    }
}

