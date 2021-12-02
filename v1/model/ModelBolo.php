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
            $this->_nomeDetalhado = $_REQUEST["nomeDetalhado"] ?? $dadosBolo->nomeDetalhado ?? null;
            $this->_nomeCard = $_REQUEST["nomeCard"] ?? $dadosBolo->nomeCard ?? null;
            $this->_precoQuilo = $_REQUEST["precoQuilo"] ?? $dadosBolo->precoQuilo ?? null;
            $this->_descricao = $_REQUEST["descricao"] ?? $dadosBolo->descricao ?? null;

            $this->_conn= $conn; 
    }

    function findOne() {
        $sqlfindOne = "SELECT * FROM tblbolo WHERE idBolo = ?";

        $statement = $this->_conn->prepare($sqlfindOne); 
        $statement->bindValue(1, $this->_idBolo);
        $statement->execute(); 
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    function findMany() {
            $sqlFindMany = "SELECT * FROM tblbolo";

            $statement= $this->_conn->prepare($sqlFindMany); 
            $statement->execute(); 
            return $statement->fetchAll(\PDO::FETCH_ASSOC); 
    }

    function create() {

        //NECESSÁRIO FAZER O RECEBIMENTO DE IMAGEMS. INDA NÃO FINALIZADO!!!!!
        $sqlCreate = "INSERT INTO tblbolo (nomeDetalhado, nomeCard, precoPorQuilo, descricao) 
        VALUES (?, ?, ?, ?)";

        $statement = $this->_conn->prepare($sqlCreate); 
        $statement->bindValue(1, $this->_nomeDetalhado);
        $statement->bindValue(2, $this->_nomeCard);
        $statement->bindValue(3, $this->_precoQuilo);
        $statement->bindValue(4, $this->_descricao);

        if ($statement->execute()){
            return "Success";
        }
        else{
            return "Error";
        }
    }

    function update($id, $data) {

    }

    function delete($id) {

    }
}

?>