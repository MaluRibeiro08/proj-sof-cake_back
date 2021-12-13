<?php
    $root = $_SERVER['DOCUMENT_ROOT'] . '/softcake/backend/v1/';
    $root = strpos($_SERVER["REQUEST_URI"], "/token") ||
            strpos($_SERVER["REQUEST_URI"], "/cadastro") ||
            strpos($_SERVER["REQUEST_URI"], "/login") ? "../../" : "../";
    require("$root/utils/Resposta.php");

    class ControllerPerfil {
        private $_metodo;
        private $_modelPerfil;
        private $_acao;

        public function __construct($model, $acao) {
            $this->_modelPerfil = $model;
            $this->_acao = $acao;
            $this->_metodo = $_SERVER['REQUEST_METHOD'];
        }

        public function router() {
            if($this->_acao == null) {
                switch($this->_metodo) {
                    case 'PUT':
                        return $this->_modelPerfil->update();
                        break;
                    case 'DELETE':
                        return $this->_modelPerfil->delete();
                }
            } else {
                switch ($this->_acao) {
                    case 'login':
                        return $this->_modelPerfil->login();
                        break;
                    case 'cadastro':
                        return $this->_modelPerfil->cadastro();
                        break;
                    case 'token':
                        return $this->_modelPerfil->verifyToken();
                        break;
                }
            }
            
        }
    }

?>