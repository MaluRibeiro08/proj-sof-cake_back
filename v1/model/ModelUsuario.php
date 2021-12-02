<?php

class ModelUsuario {

    private $_conn;
    private $_idUsuario;
    private $_nome;
    private $_cpf;
    private $_telefone;
    private $_eAdmin;

    public function __construct($conn) {

        //PERMITE RECEBER DADOS JSON ATRAVÉS DA REQUISIÇÃO
        $json = file_get_contents("php://input");
        $dadosUsuario = json_decode($json);

        //RECEBIMENTO DOS DADOS VINDOS DO POSTMAN (sejam pela URL/GET ou pelo corpo/POST)
        $this->_idUsuario = $_REQUEST["idUsuario"] ?? $dadosUsuario->idUsuario ?? null;
        $this->_nome = $_REQUEST["nome"] ?? $dadosUsuario->nome ?? null;
        $this->_cpf = $_REQUEST["cpf"] ?? $dadosUsuario->cpf ?? null;
        $this->_telefone = $_REQUEST["telefone"] ?? $dadosUsuario->telefone ?? null;
        $this->_eAdmin = $_REQUEST["eAdmin"] ?? $dadosUsuario->eAdmin ?? null;

        $this->_conn = $conn;

    }

    public function findAll() {

        //MONSTA A INSTRUÇÃO SQL
        $sql = "SELECT * FROM tblusuario";

        //PREPARA UM PROCESSO DE EXECUÇÃO DE INSTRUÇÃO SQL
        $stm = $this->_conn->prepare($sql);

        //EXECUTA A INSTRUÇÃO SQL
        $stm->execute();

        //DEVOLVE OS VALORES DA SELECT PARA SEREM UTILIZADOS
        return $stm->fetchAll(\PDO:: FETCH_ASSOC);

    }

    public function findById() {

        //MONTA A INSTRUÇÃO SQL
        $sql = "SELECT * FROM tblusuario WHERE idUsuario = ?";

        //PREPARA UM PROCESSO DE EXECUÇÃO DE INSTRUÇÃO SQL
        $stm = $this->_conn->prepare($sql);
        $stm->bindValue(1, $this->_idUsuario);

        $stm->execute();

        return $stm->fetchAll(\PDO:: FETCH_ASSOC);

    }

    public function create() {

        $sql = "INSERT INTO tblusuario (nome, cpf, telefone, eAdmin) VALUES (?, ?, ?, ?)";

        // $extensao = pathinfo($this->_fotografia, PATHINFO_EXTENSION);
        // $novoNomeArquivo = md5(microtime()) . ".$extensao";

        // move_uploaded_file($_FILES["fotografia"]["tmp_name"], "../upload/$novoNomeArquivo");

        $stm = $this->_conn->prepare($sql);

        $stm->bindValue(1, $this->_nome);
        $stm->bindValue(2, $this->_cpf);
        $stm->bindValue(3, $this->_telefone);
        $stm->bindValue(4, $this->_eAdmin);

        if ($stm->execute()) {
            return "Sucess";
        } else {
            return "Error";
        }

    }

    public function delete(){

        $sql = "DELETE FROM tblusuario WHERE idUsuario = ?";

        $stmt = $this->_conn->prepare($sql);

        $stmt->bindValue(1, $this->_idUsuario);

        if ($stmt->execute()) {
            return "Dados excluídos com sucesso!";
        }

    }

    public function update(){

        $sql = "UPDATE tblusuario SET 
        nome = ?,
        cpf = ?,
        telefone = ?,
        eAdmin = ?,
        WHERE idusuario = ?";

        $stmt = $this->_conn->prepare($sql);

        $stm->bindValue(1, $this->_nome);
        $stm->bindValue(2, $this->_cpf);
        $stm->bindValue(3, $this->_telefone);
        $stm->bindValue(4, $this->_eAdmin);
        $stmt->bindValue(5, $this->_idUsuario);

        if ($stmt->execute()) {
            return "Dados alterados com sucesso!";
        }

    }

}