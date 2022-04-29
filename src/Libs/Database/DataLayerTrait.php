<?php
namespace App\Libs\Database;
/**
 * CLASSE TRAIT DO DATABASE
 *  Esta classe de métodos de execução no banco
 *
 * @author Bruno Morais <brunomoraisti@gmail.com>
 * @version 6
 * @copyright GPL © 2021, bmorais.com
 * @package php
 * @subpackage class
 * @access private
 */

use DateTime;
use Exception;
use PDO;
use PDOException;


trait DataLayerTrait
{
    /** @var PDO */
    protected $instance;
    protected $prepare;
    protected $params;

    private function getInstance(){
        if (!isset($this->instance)) {
            $this->instance = Connect::getInstance();
            return $this->instance;
        } else {
            return $this->instance;
        }
    }

    /**
     * FUNÇÃOO PARA EXECUTAR SQL
     */
    protected function executeSQL(String $query, ?array $params = null){
        try {
            $this->getInstance();
            $prepare =  $this->instance->prepare($query);
            $prepare->execute($params);
        } catch (PDOException $e) {
            Connect::setError($e,$query);
            return false;
        }

        return $prepare;
    }

    /*$sql = "SELECT * FROM event WHERE eventdate >= :from AND eventdate <= :until AND ( user_name LIKE :st OR site_name LIKE :st )ORDER BY eventdate, start_time LIMIT 100";
    $values = array( 'st'    => '%'.$searchterm.'%','from'  => $fromdate, 'until' => $untildate, );*/
    protected function executeSQLBindValue($sql, $values, $types = false)
    {
        try {
            $this->getInstance();
            $stmt =  $this->instance->prepare($sql);
            
            foreach ($values as $key => $value) {
                if ($types) {
                    $stmt->bindValue(":$key", $value, $types[$key]);
                } else {
                    if (is_int($value)) {
                        $param = PDO::PARAM_INT;
                    } elseif (is_bool($value)) {
                        $param = PDO::PARAM_BOOL;
                    } elseif (is_null($value)) {
                        $param = PDO::PARAM_NULL;
                    } elseif (is_string($value)) {
                        $param = PDO::PARAM_STR;
                    } else {
                        $param = FALSE;
                    }

                    if ($param) $stmt->bindValue(":$key", $value, $param);
                }
            }

            $stmt->execute();

        } catch (PDOException $e) {
            Connect::setError($e,$sql);
            return false;
        }

        return $stmt;

    }

    /**
     * FUN��O PARA RETORNAR A QUANTIDADE DE ELEMENTOS DE UM OBJETO SQL
     */
    protected function count($prepare): int
    {
        try {
            $qtd = $prepare->rowCount();
            return $qtd;
        } catch (PDOException $e) {
            Connect::setError($e);
            return false;
        }


    }

    /**
     * FUN��O CONSULTA FEITA E RETORNA UM ARRAY DE OBJETOS
     */
    protected function getObjAssoc($prepare){
        try {
            $dados = $prepare->fetchAll(PDO::FETCH_ASSOC);
            return $dados;
        } catch (PDOException $e) {
            Connect::setError($e);
            return false;
        }
    }

    /**
     * FUN��O CONSULTA FEITA E RETORNA UM ARRAY DE OBJETOS
     */
    protected function getObj($prepare){
        try {
            $dados = $prepare->fetchAll(PDO::FETCH_OBJ);
            return $dados;
        } catch (PDOException $e) {
            Connect::setError($e);
            return false;
        }
    }

    protected function getObjModel($prepare, String $class){
        try {
            $dados = $prepare->fetchObject("App\\Models\\".$class);
            return $dados;
        } catch (PDOException $e) {
            Connect::setError($e);
            return false;
        }
    }

    protected function startTransaction()
    {
        $this->getInstance();

        try {
            $this->instance->beginTransaction();
        } catch (PDOException $e) {
            Connect::setError($e);
            return false;
        }

    }

    protected function commitTransaction()
    {
        try {
            $this->instance->commit();
        } catch (PDOException $e) {
            Connect::setError($e);
            return false;
        }
    }

    protected function cancelTransaction()
    {
        try {
            $this->instance->rollBack();;
        } catch (PDOException $e) {
            Connect::setError($e);
            return false;
        }
    }

    /**
     * RETORNAR O ULTIMO ID INSERIDO
     */
    protected function lastInsertId()
    {
        $ultimo = $this->instance->lastInsertId();
        return $ultimo;

    }

    protected function executeSQLTemporaria(String $query, ?array $params = null)
    {
        try {
            $result = $this->instance->prepare($query);
            $result->execute($params);
        } catch (PDOException $e) {
            Connect::setError($e,$query);
            return false;
        }

        return $result;
    }

    //METODOS ORIENTADO A OBJETO
    /*Método select que retorna um VO ou um array de objetos*/
    protected function selectDB($sql, $params = null, $class = null)
    {
        try {
            $this->getInstance();
            $query = $this->instance->prepare($sql);
            $query->execute($params);

            if (!empty($class)) {
                $rs = $query->fetchAll(PDO::FETCH_CLASS, 'App\Models\\' . $class);
            } else {
                $rs = $query->fetchAll(PDO::FETCH_OBJ);
            }
        } catch (PDOException $e) {
            Connect::setError($e,$sql);
            return false;
        }
        return $rs;
    }

    /*Método insert que insere valores no banco de dados e retorna o último id inserido*/
    protected function insertDB($sql, $params = null)
    {
        try {
            $this->getInstance();
            $query = $this->instance->prepare($sql);
            $rs = $query->execute($params);
        } catch (PDOException $e) {
            Connect::setError($e,$sql);
            return false;
        }
        return $rs;
    }

    /*Método update que altera valores do banco de dados e retorna o número de linhas afetadas*/
    protected function updateDB($sql, $params = null)
    {
        try {
            $this->getInstance();
            $query = $this->instance->prepare($sql);
            $rs = $query->execute($params);
        } catch (PDOException $e) {
            Connect::setError($e,$sql);
            return false;
        }
        return $rs;
    }

    /*Método delete que excluí valores do banco de dados retorna o número de linhas afetadas*/
    protected function deleteDB($sql, $params = null)
    {
        try {
            $this->getInstance();
            $query = $this->instance->prepare($sql);
            $rs = $query->execute($params);
        } catch (PDOException $e) {
            Connect::setError($e,$sql);
            return false;
        }
        return $rs;
    }
}

