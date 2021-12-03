<?php
    require('../utils/Resposta.php');

    class ControllerIngrediente {
        private $_metodo;
        private $_modelIngrediente;


        public function __construct($model) {
        $this->_modelIngrediente = $model;
        $this->_metodo = $_SERVER['REQUEST_METHOD'];

        $json = file_get_contents('php://input');
        $data = json_decode($json);

        $this->_ingrediente['id'] = $_REQUEST['id'] ?? $data->id ?? null;
        $this->_ingrediente['nome'] = $_REQUEST['nome'] ?? $data->nome ?? null;
        }

        public function router() {
            switch ($this->_metodo) {
                case 'GET':
                    if($this->_ingrediente['id'] !== null) {
                        return $this->_modelIngrediente->findOne();
                    }
                    return $this->_modelIngrediente->findMany();
                    break;
                case 'POST':
                    return $this->_modelIngrediente->create();
                    break;
                case 'PUT':
                    return $this->_modelIngrediente->update();
                break;
                case 'DELETE':
                    return $this->_modelIngrediente->delete();
                    break;
                default:
                    return gerarResposta('Method not allowed');
                    break;
            }
        }
    }

?>