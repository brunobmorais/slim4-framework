<?php

namespace App\Daos;

use App\Libs\Database\Connect;
use App\Libs\Database\Crud;
use App\Libs\FuncoesClass;
use App\Libs\JwtTokenClass;
use App\Libs\UuidClass;

class TokenDao extends Crud
{

    public function __construct()
    {
        $this->tableName = "TOKEN";
        $this->classModel = "TokenModel";
    }

    /**
     * @param $codusuario
     * @return false|string
     */
    public function inserirAccessToken($codusuario)
    {

        $funcoesClass = new FuncoesClass();
        $jwtTokenClass = new JwtTokenClass();
        $codsession = UuidClass::getUuidString();

        //$this->update(["SITUACAO"],["0"],"CODUSUARIO={$codusuario}");
        $datacriacao = $funcoesClass->pegarDataAtualBanco();
        $datavalidade = $funcoesClass->somaHoraDataBanco($datacriacao, 24); // VALIDADE DE 1 MES
        $token = $jwtTokenClass->encode(1440,["CODUSUARIO" => $codusuario]); // VALIDADE DE 1 MES

        // CANCELA TODOS OS TOKENS EXISTENTES PARA O USUARIO
        //$this->update(array("SITUACAO"), [0,$codusuario],"CODUSUARIO=? AND REFRESHTOKEN=0");

        // INSERE NOVO TOKEN
        $this->insert("CODTOKEN, TOKEN, DATACRIACAO, DATAVALIDADE, CODUSUARIO", [$codsession, $token, $datacriacao, $datavalidade, $codusuario]);
        return $token;
    }

    /**
     * @param $codusuario
     * @return false|string
     */
    public function inserirRefreshToken($codusuario)
    {

        $funcoesClass = new FuncoesClass();
        $jwtTokenClass = new JwtTokenClass();
        $codsession = UuidClass::getUuidString();

        $datacriacao = $funcoesClass->pegarDataAtualBanco();
        $datavalidade = $funcoesClass->somaHoraDataBanco($datacriacao, 4320); // VALIDADE DE 6 MESES
        $token = $jwtTokenClass->encode(259200,["CODUSUARIO" => $codusuario]); // VALIDADE DE 6 MESES

        // CANCELA TODOS OS TOKENS EXISTENTES PARA O USUARIO
        // $this->update(array("SITUACAO"), [0,$codusuario],"CODUSUARIO=? AND REFRESHTOKEN=1");

        // INSERE NOVO TOKEN
        $this->insert("CODTOKEN, TOKEN, REFRESHTOKEN, DATACRIACAO, DATAVALIDADE, CODUSUARIO", [$codsession, $token, 1, $datacriacao, $datavalidade, $codusuario]);
        return $token;
    }

    public function validaToken(string $token,String $codusuario)
    {
        $funcoesClass = new FuncoesClass();
        $usuarioDao = new UsuarioDao();

        $obj = $this->select("TOKEN, DATACRIACAO, DATAVALIDADE, SITUACAO, CODUSUARIO", "WHERE TOKEN LIKE ? AND CODUSUARIO=? AND SITUACAO=1", [$token,$codusuario]);
        if (!empty($obj)) {
            if ($obj[0]->DATAVALIDADE > $funcoesClass->pegarDataAtualBanco()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }

    }

    public function setTokenVencido($token){

        $sql = "UPDATE TOKEN SET SITUACAO=0 WHERE TOKEN=?";
        $result = $this->executeSQL($sql,[$token]);
        if ($result){
            return true;
        } else {
            return false;
        }

    }
}