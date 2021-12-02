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
            $this->_idBolo = $dadosBolo["idBolo"];
            $this->_nomeDetalhado = $dadosBolo["nomeDetalhado"];
            $this->_nomeCard = $dadosBolo["nomeCard"];
            $this->_precoQuilo = $dadosBolo["precoQuilo"];
            $this->_descricao = $dadosBolo["descricao"];

            $this->_conn= $conn; 
    }

    function findOne($id) {

    }

    function findMany() {
        //criando a instrucao SQL
            $sqlSelectAll = "SELECT * FROM tblbolo";
                                
        //executando a instrução e retornando dados
            $sqlSelectAll= $this->_conn->prepare($sqlSelectAll); 
            $sqlSelectAll->execute(); 
            return $sqlSelectAll->fetchAll(\PDO::FETCH_ASSOC); //só retorna array associativo 
    }

    function create($data) {

    }

    function update($id, $data) {

    }

    function delete($id) {

    }
}

?>