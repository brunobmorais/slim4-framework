<?php
namespace App\Libs\Database;

/**
 * CLASSE CRUD
 * Classe abastrada para fazer ligação entre o banco e aplicação
 *
 * @author Bruno Morais <brunomoraisti@gmail.com>
 * @version 2
 * @date 2022-04-05
 * @copyright GPL © 2022, bmorais.com
 * @package php
 * @subpackage class
 * @access private
 */


abstract class Crud {

    use DataLayerTrait;

    protected $classModel;
    protected $tableName;


    /* BUSCAR
    * $dao = new Dao();
    * $dao->buscar("*","WHERE SITUACAO=1 AND EXCLUIDO=0 ORDER BY CODPLANO");
    */
    /**
     * @param string $atributos
     * @param string $add
     * @param array $parametros
     * @param false $fetchobj
     * @param false $debug
     * @return array|false|void
     */
    public function select(string $atributos="*", string $add="", array $parametros= null, bool $fetchobj=true, bool $debug=false){
        if(strlen($add)>0)
            $add = " ".$add;
        $sql = "SELECT {$atributos} FROM {$this->tableName}{$add}";
        if ($debug){echo $sql; exit;}
        if (!$fetchobj)
            return $this->selectDB($sql,$parametros,$this->classModel);
        else
            return $this->selectDB($sql,$parametros,null);
    }

    /* INSERIR
     * $dao = new Dao();
     * $atributos = "DATAPAGAMENTO, VALOR";
     * $parametros = array($response['date'], $response['grossAmount']);
     * $dao->inserir($atributos,$parametros);*/
    /**
     * @param string $atributos
     * @param array $parametros
     * @param false $debug
     * @return bool|void
     */
    public function insert(String $atributos, array $parametros=null, $debug=false){
        $numparams="";
        for($i=0; $i<count($parametros); $i++)
            $numparams.=",?";
        $numparams = substr($numparams,1);
        $sql = "INSERT INTO ".$this->tableName." ($atributos) VALUES ($numparams)";
        if ($debug){echo $sql; var_dump($parametros);exit;}
        $t=$this->insertDB($sql,$parametros);
        return $t;
    }

    // ATUALIZAR
    /* $dao = new Dao();
     * $atributos = array("EXCLUIDO");
     * $parametros = array("1");
     * $where = "CODUSUARIO = '" . $codusuario . "'";
     * $result = $dao->atualizar($atributos, $parametros, $where);*/
    /**
     * @param array $atributos
     * @param array $parametros
     * @param string $where
     * @param false $debug
     * @return bool|void
     */
    public function update(array $atributos, array $parametros=null, string $where=null, bool $debug=false){
        $fields_T="";
        for($i=0; $i<count($atributos); $i++) $fields_T.=", $atributos[$i] = ?";
        $fields_T = substr($fields_T,2);
        $sql = "UPDATE ".$this->tableName." SET $fields_T";
        if(isset($where)) $sql .= " WHERE $where";
        if ($debug){echo $sql; var_dump($parametros); exit;}
        $t=$this->updateDB($sql,$parametros);
        return $t;
    }

    // REMOVER
    /* $dao = new Dao();
     * $parametros = array('1');
     * $where = "ID";
     * $result = $dao->remover($where);*/
    /**
     * @param string $where
     * @param array $parametros
     * @param false $debug
     * @return bool|void
     */
    public function delete(array $parametros=null, string $where=null, bool $debug=false){
        $sql = "DELETE FROM ".$this->tableName;
        if(isset($where)) $sql .= " WHERE $where";
        if ($debug){echo $sql; exit;}
        $t=$this->deleteDB($sql,$parametros);
        return $t;
    }

}