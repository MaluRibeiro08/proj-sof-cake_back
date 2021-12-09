<?php
    require('../utils/Resposta.php');

    class ControllerPerfil {
        private $_modelPerfil;

        public function __construct($model) {
            $this->_modelPerfil = $model;
        }

        public function router() {
            switch ($_REQUEST['acao']) {
                case 'login':
                    return $this->_modelPerfil->login();
                    break;
                case 'cadastro':
                    return $this->_modelPerfil->cadastro();
                    break;
                case 'update':
                    return $this->_modelPerfil->update();
                    break;
                case 'delete':
                    return $this->_modelPerfil->delete();
                    break;
                case 'get':
                    return $this->_modelPerfil->verifyToken();
                    break;
            }
        }
    }

?>