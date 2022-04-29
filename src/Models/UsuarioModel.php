<?php

namespace App\Models;

class UsuarioModel
{

    private $CODUSUARIO;
    private $IMAGEM;
    private $NOME;
    private $EMAIL;
    private $TELEFONE;
    private $SENHA;
    private $SITUACAO;
    private $EXCLUIDO;
    private $ULTIMOACESSO;

    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getCODUSUARIO()
    {
        return $this->CODUSUARIO;
    }

    /**
     * @param mixed $CODUSUARIO
     * @return UsuarioModel
     */
    public function setCODUSUARIO($CODUSUARIO)
    {
        $this->CODUSUARIO = $CODUSUARIO;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIMAGEM()
    {
        return $this->IMAGEM;
    }

    /**
     * @param mixed $IMAGEM
     * @return UsuarioModel
     */
    public function setIMAGEM($IMAGEM)
    {
        $this->IMAGEM = $IMAGEM;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNOME()
    {
        return $this->NOME;
    }

    /**
     * @param mixed $NOME
     * @return UsuarioModel
     */
    public function setNOME($NOME)
    {
        $this->NOME = $NOME;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEMAIL()
    {
        return $this->EMAIL;
    }

    /**
     * @param mixed $EMAIL
     * @return UsuarioModel
     */
    public function setEMAIL($EMAIL)
    {
        $this->EMAIL = $EMAIL;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTELEFONE()
    {
        return $this->TELEFONE;
    }

    /**
     * @param mixed $TELEFONE
     * @return UsuarioModel
     */
    public function setTELEFONE($TELEFONE)
    {
        $this->TELEFONE = $TELEFONE;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSENHA()
    {
        return $this->SENHA;
    }

    /**
     * @param mixed $SENHA
     * @return UsuarioModel
     */
    public function setSENHA($SENHA)
    {
        $this->SENHA = $SENHA;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSITUACAO()
    {
        return $this->SITUACAO;
    }

    /**
     * @param mixed $SITUACAO
     * @return UsuarioModel
     */
    public function setSITUACAO($SITUACAO)
    {
        $this->SITUACAO = $SITUACAO;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEXCLUIDO()
    {
        return $this->EXCLUIDO;
    }

    /**
     * @param mixed $EXCLUIDO
     * @return UsuarioModel
     */
    public function setEXCLUIDO($EXCLUIDO)
    {
        $this->EXCLUIDO = $EXCLUIDO;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getULTIMOACESSO()
    {
        return $this->ULTIMOACESSO;
    }

    /**
     * @param mixed $ULTIMOACESSO
     * @return UsuarioModel
     */
    public function setULTIMOACESSO($ULTIMOACESSO)
    {
        $this->ULTIMOACESSO = $ULTIMOACESSO;
        return $this;
    }

}