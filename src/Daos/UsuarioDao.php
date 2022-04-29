<?php
namespace App\Daos;

use App\Libs\Database\Connect;
use App\Libs\Database\Crud;
use App\Libs\FuncoesClass;
use App\Libs\JwtTokenClass;
use App\Libs\UuidClass;
use App\Models\UsuarioModel;
use MongoDB\BSON\Binary;
use mysql_xdevapi\Session;
use Ramsey\Uuid\Exception\UnableToBuildUuidException;

class UsuarioDao extends Crud {

    public function __construct()
    {
        $this->tableName = "USUARIO";
        $this->classModel = "UsuarioModel";
    }

    public function insertUsuario($objJson = []):array{

        $usuarioDao = new UsuarioDao();
        $funcoesClass = new FuncoesClass();
        $jwtTokenClass = new JwtTokenClass();
        $tokenDao = new TokenDao();

        $nome = $objJson['NOME']??'';
        $email = $objJson['EMAIL']??'';
        $telefone = $funcoesClass->removeCaracteres($objJson['TELEFONE']??'');
        $senha = $funcoesClass->create_password_hash($objJson['SENHA']??'');
        $dataAtual = $funcoesClass->pegarDataAtualBanco();
        $hash = $objJson['HASH']??'';
        $device = $objJson['DEVICE']??'';
        $type = $objJson['TYPE']??'';

        $codusuarioBin = UuidClass::getUuidString();

        if (!empty($nome) && !empty($email) && !empty($hash) && !empty($device)) {
            if (empty($this->select("EMAIL", "WHERE EMAIL=?", [$email]))) {
                $this->startTransaction();
                if ($usuarioDao->insert("CODUSUARIO, NOME, EMAIL, TELEFONE, SENHA, ULTIMOACESSO", [$codusuarioBin, $nome, $email, $telefone, $senha, $dataAtual])) {

                    $this->commitTransaction();
                    $data = $this->buscarInformacoesUsuario($codusuarioBin);
                    $data['CODE'] = '2000';
                    $data['TOKEN'] = $tokenDao->inserirAccessToken($codusuarioBin);
                    $data['REFRESHTOKEN'] = $tokenDao->inserirRefreshToken($codusuarioBin);

                } else {
                    $this->cancelTransaction();
                    $data = array("ERROR" => true, "MESSAGE" => "Erro ao registrar usuário","CODE" => "4001");
                }
            } else {
                $data = array("ERROR" => false, "MESSAGE" => "Email já cadastrado!","CODE" => "2001");
            }
        } else {
            $data = array("ERROR" => true, "MESSAGE" => "Ausencia de parametros","CODE" => "4001");
        }

        return $data;
    }

    public function insertNovaSenha($objJson = []):array{

        $funcoesClass = new FuncoesClass();


        $senha = $objJson['SENHA']??'';
        $email = $objJson['EMAIL']??'';

        if (!empty($email) && !empty($senha)) {
            $senha = $funcoesClass->create_password_hash($senha);
            if (!empty($this->select("EMAIL", "WHERE EMAIL=?", [$email]))) {
                $this->startTransaction();
                if ($this->update(array("SENHA"), array($senha,$email), "EMAIL=?")) {
                    $this->commitTransaction();
                    return array("ERROR" => false, "MESSAGE" => "Alterado com sucesso", "CODE" => "2000");
                } else {
                    $this->cancelTransaction();
                    return array("ERROR" => true, "MESSAGE" => "Erro ao registrar nova senha");
                }
            } else {
                return array("ERROR" => true, "MESSAGE" => "Usuário não encontrado");
            }
        } else {
            return array("ERROR" => true, "MESSAGE" => "Ausência de parametros");
        }

    }

    /**
     * @param array $objJson
     * @return array
     */
    public function loginUsuario($objJson = []):array{

        $funcoesClass = new FuncoesClass();
        $jwtTokenClass = new JwtTokenClass();
        $sessionDao = new TokenDao();

        $email = $objJson['EMAIL']??'';
        $senha = $objJson['SENHA']??'';
        $hash = $objJson['HASH'];
        $device = $objJson['DEVICE'];
        $type = $objJson['TYPE'];
        $dataAtual = $funcoesClass->pegarDataAtualBanco();


        if (!empty($email) && !empty($senha)) {
            $usuarioModel = $this->buscarUsuarioEmail($email);

            if (!empty($usuarioModel)) {
                $codusuarioBin = $usuarioModel->getCODUSUARIO();
                if ($funcoesClass->verify_password_hash($senha, $usuarioModel->getSENHA())) {
                    $this->atualizaUsuarioUltimoAcesso($codusuarioBin);
                    $data['ERROR'] = false;
                    $data['CODE'] = '2000';
                    $data['TOKEN'] = $sessionDao->inserirAccessToken($codusuarioBin);
                    $data['REFRESHTOKEN'] = $sessionDao->inserirRefreshToken($codusuarioBin);
                } else {
                    $data = array("ERROR" => true, "MESSAGE" => "Usuário ou senha incorreto", 'CODE' => '4001');
                }
            } else {
                $data = array("ERROR" => true, "MESSAGE" => "Usuário ou senha incorreto", 'CODE' => '4001');
            }
        } else {
            $data = array("ERROR" => true, "MESSAGE" => "Preencha todos os campos", 'code' => '4001');
        }

        return $data;
    }

    /**
     * @param string $codusuarioString
     * @return array
     */
    public function buscarInformacoesUsuario($codusuarioString){

        $data = array(
            "ERROR" => false,
            "MESSAGE" => "Informações usuário",
            "CODE" => "2000",
            "USUARIO" => $this->buscarUsuarioCodusuarioString($codusuarioString),
        );

        return $data;
    }

    /**
     * @param string $email
     * @return UsuarioModel|null
     */
    public function buscarUsuarioEmail(string $email): ?UsuarioModel
    {
        $result = $this->select("*", "WHERE EMAIL=?", [$email],false);
        return $result[0]??null;
    }

    /**
     * @param $codusuarioBin
     * @return bool|void
     */
    public function atualizaUsuarioUltimoAcesso($codusuarioBin){
        $funcoesClass = new FuncoesClass();
        return $this->update(array("ULTIMOACESSO"), array($funcoesClass->pegarDataAtualBanco(), $codusuarioBin), "CODUSUARIO=?");
    }

    /**
     * @param $codusuario
     * @return false|mixed
     */
    public function buscarUsuarioCodusuarioByte($codusuario){
        $sql = "SELECT U.CODUSUARIO AS CODUSUARIO, U.IMAGEM, U.NOME, U.EMAIL, U.TELEFONE, U.SITUACAO, U.ULTIMOACESSO, U.EXCLUIDO
                    FROM USUARIO AS U
                    WHERE U.CODUSUARIO=?";
        $result = $this->executeSQL($sql,[$codusuario]);
        return $this->getObj($result);

    }

    /**
     * @return array
     */
    public function buscarUsuarioCodusuarioString($codusuario){
        $sql = "SELECT U.CODUSUARIO AS CODUSUARIO, U.IMAGEM, U.NOME, U.EMAIL, U.TELEFONE, U.SITUACAO, U.ULTIMOACESSO, U.EXCLUIDO, (SELECT COUNT(CODDISPOSITIVOS) FROM DISPOSITIVOS WHERE CODUSUARIO=U.CODUSUARIO AND EXCLUIDO=0) AS QTDDISPOSITIVOS
                    FROM USUARIO AS U
                    WHERE U.CODUSUARIO=?";
        $result = $this->executeSQL($sql,[$codusuario]);
        return $this->getObj($result);

    }



}