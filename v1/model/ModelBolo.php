<?php

class ModelBolo {
    private $_conn;
    private $_idBolo;
    private $_nomeDetalhado;
    private $_nomeCard;
    private $_precoQuilo;
    private $_descricao;
    private $_avisos;

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

        try {
            $sqlCreate = "INSERT INTO tblbolo (nomeDetalhado, nomeCard, precoPorQuilo, descricao) 
            VALUES (?, ?, ?, ?)";
    
            $statement = $this->_conn->prepare($sqlCreate); 
            $statement->bindValue(1, $this->_nomeDetalhado);
            $statement->bindValue(2, $this->_nomeCard);
            $statement->bindValue(3, $this->_precoQuilo);
            $statement->bindValue(4, $this->_descricao);

            $statement->execute();

            $this->_idBolo = $this->_conn->lastInsertId();

            foreach ($_FILES as $indice => $dadosImagem){
                
                $extensao = pathinfo($dadosImagem['name'], PATHINFO_EXTENSION);
                $novoNomeArquivo = md5(microtime()) . ".$extensao";
                move_uploaded_file($dadosImagem["tmp_name"], "../bolo/uploads/$novoNomeArquivo");

                $sqlCreateImagem= "INSERT INTO tblimagembolo (nomeArquivo, idBolo) VALUES (?, ?);";

                $statementImagem = $this->_conn->prepare($sqlCreateImagem); 
                $statementImagem->bindValue(1, $novoNomeArquivo);
                $statementImagem->bindValue(2, $this->_idBolo);
                $statementImagem->execute();
            }
        

            return gerarResposta("Bolo cadastrado com sucesso");

        } catch (PDOException $error) {
            return gerarResposta($error->getMessage(), 'erro');
        }
    }

    function update($id, $data) {

    }

    function delete($id) {

    }
}

?>