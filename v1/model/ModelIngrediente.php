<?php
    class ModelIngrediente {
        private $_conn;

        function __construct($conn) {
            $this->_conn = $conn;
        }

        function findOne($id) {
            $sql = "SELECT * FROM tblIngrediente WHERE idIngrediente = :id";
            $stm = $this->_conn->prepare($sql);
            $stm->bindParam(":id", $id);
            $stm->execute();
            return gerarResposta($stm->fetchAll(PDO::FETCH_ASSOC));
        }

        function findMany() {
            $sql = "SELECT * FROM tblIngrediente";
            $stm = $this->_conn->prepare($sql);
            $stm->execute();
            return gerarResposta($stm->fetchAll(PDO::FETCH_ASSOC));
        }

        function create($data) {
            try {
                $sql = "INSERT INTO tblIngrediente (nome) VALUES (:nome)";
                $stm = $this->_conn->prepare($sql);
                $stm->bindParam(":nome", $data["nome"]);
                $stm->execute();

                return gerarResposta($this->_conn->lastInsertId());
              } catch (PDOException $error) {
                return gerarResposta($error->getMessage(), 'erro');
              }
        }

        function update($id, $data) {
            try {
                $sql = "UPDATE tblIngrediente SET nome = :nome  WHERE idIngrediente = :id";
                $stm = $this->_conn->prepare($sql);
                $stm->bindParam(":nome", $data["nome"]);
                $stm->bindParam(":id", $id);
                $stm->execute();

                return gerarResposta("Ingrediente $id atualizado com sucesso");
              } catch (PDOException $error) {
                return gerarResposta($error->getMessage(), 'erro');
              }
        }

        function delete($id) {
            try {
                $sql = "DELETE FROM tblIngrediente WHERE idIngrediente = :id";
                $stm = $this->_conn->prepare($sql);
                $stm->bindParam(":id", $id);
                $stm->execute();

                return gerarResposta("Ingrediente $id deletado com sucesso");
              } catch (PDOException $error) {
                return gerarResposta($error->getMessage(), 'erro');
              }
        }
    }

?>