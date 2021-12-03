<?php

class ModelUsuario {

    private $_conn;
    private $_idUsuario;
    private $_nome;
    private $_cpf;
    private $_telefone;
    private $_eAdmin;

    public function __construct($conn) {
        $json = file_get_contents("php://input");
        $dadosUsuario = json_decode($json);

        $this->_idUsuario = $_REQUEST["idUsuario"] ?? $dadosUsuario->idUsuario ?? null;
        $this->_nome = $_REQUEST["nome"] ?? $dadosUsuario->nome ?? null;
        $this->_cpf = $_REQUEST["cpf"] ?? $dadosUsuario->cpf ?? null;
        $this->_telefone = $_REQUEST["telefone"] ?? $dadosUsuario->telefone ?? null;
        $this->_eAdmin = $_REQUEST["eAdmin"] ?? $dadosUsuario->eAdmin ?? null;

        $this->_conn = $conn;
    }

    public function findOne() {
        $sql = "SELECT * FROM tblusuario WHERE idUsuario = :idUsuario";

        $stm = $this->_conn->prepare($sql);
        $stm->bindParam(":idUsuario", $this->_idUsuario);

        $stm->execute();
        return $stm->fetchAll(\PDO:: FETCH_ASSOC);
    }

    public function findMany() {
        $sql = "SELECT * FROM tblusuario";

        $stm = $this->_conn->prepare($sql);

        $stm->execute();
        return $stm->fetchAll(\PDO:: FETCH_ASSOC);
    }

    public function create() {
        try {
            $sql = "INSERT INTO tblusuario (nome, cpf, telefone, eAdmin) VALUES (:nome, :cpf, :telefone, :eAdmin)";

            // $extensao = pathinfo($this->_fotografia, PATHINFO_EXTENSION);
            // $novoNomeArquivo = md5(microtime()) . ".$extensao";

            // move_uploaded_file($_FILES["fotografia"]["tmp_name"], "../upload/$novoNomeArquivo");

            $stm = $this->_conn->prepare($sql);
            $stm->bindParam(":nome", $this->_nome);
            $stm->bindParam(":cpf", $this->_cpf);
            $stm->bindParam(":telefone", $this->_telefone);
            $stm->bindParam(":eAdmin", $this->_eAdmin);

            $stm->execute();
            return gerarResposta("Usuário cadastrado com sucesso");
        } catch (PDOException $error) {
            return gerarResposta($error->getMessage(), 'erro');
        }
    }

    public function delete(){
        try {
            $sql = "DELETE FROM tblusuario WHERE idUsuario = :idUsuario";

            $stmt = $this->_conn->prepare($sql);
            $stmt->bindParam(":idUsuario", $this->_idUsuario);

            $stmt->execute();
            return gerarResposta("Usuário removido com sucesso");
        } catch (PDOException $error) {
            return gerarResposta($error->getMessage(), 'erro');
        }

    }

    public function update(){
        try {
            $sql = "UPDATE tblusuario SET nome = :nome, cpf = :cpf, telefone = :telefone, eAdmin = :eAdmin, WHERE idusuario = :idUsuario";
            
            $stm = $this->_conn->prepare($sql);
            $stm->bindParam(":nome", $this->_nome);
            $stm->bindParam(":cpf", $this->_cpf);
            $stm->bindParam(":telefone", $this->_telefone);
            $stm->bindParam(":eAdmin", $this->_eAdmin);
            $stm->bindParam(":idUsuario", $this->_idUsuario);

            $stm->execute();
            return gerarResposta("Usuário atualizado com sucesso");
        } catch (PDOException $error) {
            return gerarResposta($error->getMessage(), 'erro');
        }

    }

}