<?php

class ModelBolo {
    private $_conn;
    private $_idBolo;
    private $_nomeDetalhado;
    private $_nomeCard;
    private $_precoQuilo;
    private $_descricao;

    function __construct($conn) {
       
        //permissão para receber dados em JSON através da requisição
            $json = file_get_contents("php://input");
            //aceita todos os tipos de dados
            $dadosBolo = json_decode($json);
            //pegas os dados e transformam em array

        //atribuindo infomações que vieram aos atributos da calsse
            $this->_conn = $conn;
            $this->_idBolo = $_REQUEST["idBolo"] ?? $dadosBolo->idBolo ?? null;
            $this->_nomeDetalhado = $_REQUEST["idBolo"] ?? $dadosBolo->nomeDetalhado ?? null;
            $this->_nomeCard = $_REQUEST["idBolo"] ?? $dadosBolo->nomeCard ?? null;
            $this->_precoQuilo = $_REQUEST["idBolo"] ?? $dadosBolo->precoQuilo ?? null;
            $this->_descricao = $_REQUEST["idBolo"] ?? $dadosBolo->descricao ?? null;

            $this->_conn= $conn; 
    }

    function findOne() {
        $sqlfindOne = "SELECT * FROM tblbolo WHERE idBolo = ?";

        $statement = $this->_conn->prepare($sqlfindOne); 
        $statement->bindValue(1, $this->_idBolo);
        $statement->execute(); 
        return $statement->fetchAll(\PDO::FETCH_ASSOC); //só retorna array a
    }

    function findMany() {
        //criando a instrucao SQL
            $sqlFindMany = "SELECT * FROM tblbolo";
                                
        //executando a instrução e retornando dados
            $statement= $this->_conn->prepare($sqlFindMany); 
            $statement->execute(); 
            return $statement->fetchAll(\PDO::FETCH_ASSOC); //só retorna array associativo 
    }

    function create($data) {

    }

    function update($id, $data) {

    }

    function delete($id) {

    }
}

?>