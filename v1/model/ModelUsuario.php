<?php

class ModelUsuario {

    private $_conn;
    private $_idUsuario;
    private $_nome;
    private $_cpf;
    private $_telefone;
    private $_eAdmin;
    private $_email;
    private $_senha;
    private $_foto;
    

    public function __construct($conn) {
        $json = file_get_contents("php://input");
        $dadosUsuario = json_decode($json);

        $this->_idUsuario = $_REQUEST["idUsuario"] ?? $dadosUsuario->idUsuario ?? null;
        $this->_nome = $_REQUEST["nome"] ?? $dadosUsuario->nome ?? null;
        $this->_cpf = $_REQUEST["cpf"] ?? $dadosUsuario->cpf ?? null;
        $this->_telefone = $_REQUEST["telefone"] ?? $dadosUsuario->telefone ?? null;
        $this->_eAdmin = $_REQUEST["eAdmin"] ?? $dadosUsuario->eAdmin ?? null;
        $this->_email = $_REQUEST["email"] ?? $dadosUsuario->email ?? null;
        $this->_senha = $_REQUEST["senha"] ?? $dadosUsuario->senha ?? null;
        $this->_foto = $_REQUEST["foto"] ?? $dadosUsuario->foto ?? null;

        $this->_conn = $conn;
    }

    // Primeiro você vai obter os dados do tblUsuario e salva numa variavel
    // Agora você faz outro select e verifica na tblPerfil se há um usuario com o id da tblUsuario
    // Agora você mescla as arrays


    public function findOne() {

        $sql = "SELECT * FROM tblusuario WHERE idUsuario = :idUsuario";

        $stm = $this->_conn->prepare($sql);
        $stm->bindParam(":idUsuario", $this->_idUsuario);

        $stm->execute();

        $usuario = $stm->fetchAll(\PDO:: FETCH_ASSOC);

        $sql = "SELECT * FROM tblperfil WHERE idUsuario = :idUsuario";

        $stm = $this->_conn->prepare($sql);
        $stm->bindParam(":idUsuario", $this->_idUsuario);
        $stm->execute();

        $perfil = $stm->fetchAll(\PDO:: FETCH_ASSOC);

        if($perfil !== null) {
            $dados = array_merge($usuario, $perfil);
            return $dados;
        } else {
            return $usuario;
        }

    }

    public function findMany() {
        $sql = "SELECT * FROM tblusuario";

        $stm = $this->_conn->prepare($sql);

        $stm->execute();
        return $stm->fetchAll(\PDO:: FETCH_ASSOC);
    }

    public function create() {

        try {
            $sqlUsuario = "INSERT INTO tblusuario (nome, cpf, telefone, eAdmin) VALUES (:nome, :cpf, :telefone, :eAdmin)";

            $stm = $this->_conn->prepare($sqlUsuario);
            $stm->bindParam(":nome", $this->_nome);
            $stm->bindParam(":cpf", $this->_cpf);
            $stm->bindParam(":telefone", $this->_telefone);
            $stm->bindParam(":eAdmin", $this->_eAdmin);

            $stm->execute();

            $this->_idUsuario = $this->_conn->lastInsertId();


            $sqlPerfil = "INSERT INTO tblperfil (email, senha, foto, idUsuario) VALUES (:email, :senha, :foto, :idUsuario)";
            
            $stmPerfil = $this->_conn->prepare($sqlPerfil);
            $stmPerfil->bindParam(":email", $this->_email);
            $stmPerfil->bindParam(":senha", $this->_senha);
            $stmPerfil->bindParam(":foto", $this->_foto);
            $stmPerfil->bindParam(":idUsuario", $this->_idUsuario);

             $stmPerfil->execute();

            return gerarResposta("Usuário cadastrado com sucesso");

        } catch (PDOException $error) {
            return gerarResposta($error->getMessage(), 'erro');
        }
    }

    public function delete(){

        try {

            $sql = "DELETE FROM tblperfil WHERE idUsuario = :idUsuario";

            $stm = $this->_conn->prepare($sql);
            $stm->bindParam(":idUsuario", $this->_idUsuario);
            $stm->execute();


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
            $sql = "UPDATE tblusuario SET nome = :nome, cpf = :cpf, telefone = :telefone, eAdmin = :eAdmin WHERE idUsuario = :idUsuario";
            
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