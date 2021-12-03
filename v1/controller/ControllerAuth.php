<?php
    require('../utils/Resposta.php');

    class ControllerAuth {
        private $_modelAuth;

        public function __construct($model) {
            $this->_modelAuth = $model;
        }

        public function router() {
            switch ($_REQUEST['acao']) {
                case 'login':
                    return $this->_modelAuth->login();
                    break;
                case 'signup':
                    return $this->_modelAuth->cadastro();
                    break;
                case 'get':
                    return $this->_modelAuth->verifyToken();
                    break;
            }
        }
    }

?>