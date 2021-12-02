<?php

    class ControllerUsuario {

        private $_method;
        private $_modelUsuario;
        private $_idUsuario;

        public function __construct($model) {

            $this->_modelUsuario = $model;
            $this->_method = $_SERVER['REQUEST_METHOD'];

            //PERMITE RECEBER DADOS JSON ATRAVÉS DA REQUISIÇÃO
            $json = file_get_contents("php://input");
            $dadosUsuario = json_decode($json);

            //se tiver algo, passa essa alguma coisa. Se não, passa nulo
            $this->_idUsuario = $_REQUEST["idUsuario"] ?? $dadosUsuario->idUsuario ?? null;
        }

        function router() {

            switch ($this->_method) {
                case 'GET':

                    if (isset($this->_idUsuario)) {
                        return $this->_modelUsuario->findById();
                    }
                    return $this->_modelUsuario->findAll();
                    
                    break;

                case 'POST':
                    return $this->_modelUsuario->create();
                    break;

                case 'PUT':
                    return $this->_modelUsuario->update();
                    break;

                case 'DELETE':
                    return $this->_modelUsuario->delete();
                    break;
                
                default:
                    # code...
                    break;
            }

        }

    }