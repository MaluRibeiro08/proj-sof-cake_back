<?php
    require('../utils/Resposta.php');

    class ControllerBolo {
        private $_metodo;
        private $_modelBolo;
        private $_idBolo;
        private $_acao;

        public function __construct($modelBolo) {
            $this->_modelBolo = $modelBolo;
            $this->_metodo = $_SERVER['REQUEST_METHOD'];

            $json = file_get_contents('php://input');
            $dadosBolo = json_decode($json);

            $this->_idBolo = $_REQUEST["idBolo"] ?? $dadosBolo->idBolo ?? null;
            $this->_acao = $_REQUEST["acao"] ?? $dadosBolo->acao ?? null;
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
                    if ($this->_acao == "create") {
                        return $this->_modelBolo->create();
                        break;
                    }
                    elseif ($this->_acao == "update"){
                        return $this->_modelBolo->update(); 
                        break;
                    }
                    break;
                case 'DELETE':
                    return $this->_modelBolo->delete();
                    break;
            
                default:
                    return gerarResposta('Method not allowed', 'erro');
                    break;
            }
        }
    }

?>