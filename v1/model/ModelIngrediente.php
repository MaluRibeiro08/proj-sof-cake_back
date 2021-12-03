<?php
    class ModelIngrediente {
        private $_conn;
        private $_idIngrediente;
        private $_ingrendiente;

        function __construct($conn) {
            $this->_conn = $conn;

            $json = file_get_contents("php://input");
            $dadosUsuario = json_decode($json);
    
            $this->_idIngrediente = $_REQUEST["id"] ?? $dadosUsuario->idUsuario ?? null;
            $this->_ingrendiente = $_REQUEST["nome"] ?? $dadosUsuario->nome ?? null;
    
            $this->_conn = $conn;
        }

        function findOne() {
            $sql = "SELECT * FROM tblIngrediente WHERE idIngrediente = :id";
            $stm = $this->_conn->prepare($sql);
            $stm->bindParam(":id", $this->_idIngrediente);
            $stm->execute();
            return gerarResposta($stm->fetchAll(PDO::FETCH_ASSOC));
        }

        function findMany() {
            $sql = "SELECT * FROM tblIngrediente";
            $stm = $this->_conn->prepare($sql);
            $stm->execute();
            return gerarResposta($stm->fetchAll(PDO::FETCH_ASSOC));
        }

        function create() {
            try {
                $sql = "INSERT INTO tblIngrediente (nome) VALUES (:nome)";
                $stm = $this->_conn->prepare($sql);
                $stm->bindParam(":nome", $this->_ingrendiente);
                $stm->execute();

                return gerarResposta($this->_conn->lastInsertId());
              } catch (PDOException $error) {
                return gerarResposta($error->getMessage(), 'erro');
              }
        }

        function update() {
            try {
                $sql = "UPDATE tblIngrediente SET nome = :nome  WHERE idIngrediente = :id";
                $stm = $this->_conn->prepare($sql);
                $stm->bindParam(":nome", $this->_ingrendiente);
                $stm->bindParam(":id", $this->_idIngrediente);
                $stm->execute();

                return gerarResposta("Ingrediente $this->_idIngrediente atualizado com sucesso");
              } catch (PDOException $error) {
                return gerarResposta($error->getMessage(), 'erro');
              }
        }

        function delete() {
            try {
                $sql = "DELETE FROM tblIngrediente WHERE idIngrediente = :id";
                $stm = $this->_conn->prepare($sql);
                $stm->bindParam(":id", $this->_idIngrediente);
                $stm->execute();

                return gerarResposta("Ingrediente $this->_idIngrediente deletado com sucesso");
              } catch (PDOException $error) {
                return gerarResposta($error->getMessage(), 'erro');
              }
        }
    }

?>