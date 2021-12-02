<?php
    require('../utils/Resposta.php');

    class ControllerBolo {
        private $_metodo;
        private $_modelBolo;
        private $_idBolo;

        public function __construct($modelBolo) {
            $this->_modelBolo = $modelBolo;
            $this->_metodo = $_SERVER['REQUEST_METHOD'];

            $json = file_get_contents('php://input');
            $dadosBolo = json_decode($json);

            $this->_idBolo = $_REQUEST["idBolo"] ?? $dadosBolo->idBolo ?? null;
        }

        public function router() {
            switch ($this->_metodo) {
                case 'GET':
                    
                    if($this->_idBolo !== null) {
                        return $this->_modelBolo->findOne();
                    }
                    return $this->_modelBolo->findMany();
                    break;
                case 'POST':
                    return $this->_modelBolo->create();
                    break;
                // case 'PUT':
                //     return $this->_modelBolo->update($this->_idBolo, $this->_ingrediente);
                // break;
                // case 'DELETE':
                //     $idIngrediente = $this->_idBolo;
                //     return $this->_modelBolo->delete($idIngrediente);
                //     break;
                // default:
                //     return gerarResposta('Method not allowed');
                //     break;
            }
        }
    }

?>