<?php

require_once '../../vendor/autoload.php';
use Firebase\JWT\JWT;

class ModelPerfil {
    private $_idPerfil;
    private $_conn;
    private $_cpf;
    private $_email;
    private $_senha;
    private $_token;
    private $_foto;
    private $_nomeArquivo;
    private $_idUsuario;

    function __construct($conn) {
        $json = file_get_contents("php://input");
        $dadosUsuario = json_decode($json);

        $this->_conn = $conn;
        $this->_idPerfil = $_REQUEST["idPerfil"] ?? $dadosUsuario->idPerfil ?? null;
        $this->_nome = $_REQUEST["nome"] ?? $dadosUsuario->nome ?? null;
        $this->_cpf = $_REQUEST["cpf"] ?? $dadosUsuario->cpf ?? null;
        $this->_telefone = $_REQUEST["telefone"] ?? $dadosUsuario->telefone ?? null;
        $this->_email = $_REQUEST["email"] ?? $dadosUsuario->email ?? null;
        $this->_senha = $_REQUEST["senha"] ?? $dadosUsuario->senha ?? null;
        $this->_token = $_SERVER["HTTP_AUTHORIZATION"];
        $this->_foto = $_REQUEST["foto"] ?? $dadosUsuario->foto ?? null;
        $this->_nomeArquivo = $_REQUEST["nomeArquivo"] ?? $dadosUsuario->nomeArquivo ?? null;
        $this->_idUsuario = $_REQUEST["idUsuario"] ?? $dadosUsuario->idUsuario ?? null;

        $this->_conn= $conn; 
    }

    function cadastro() {

        $sql = "SELECT * FROM tblperfil WHERE email = :email";
        $stmt = $this->_conn->prepare($sql);
        $stmt->bindParam(":email", $this->_email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if($result) {
            return gerarResposta("Usuário já cadastrado", 'erro');
        } else {
            $sql = "SELECT * FROM tblusuario WHERE cpf = :cpf";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindParam(":cpf", $this->_cpf);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            try {
                if($result) {
                    $sql = "INSERT INTO tblperfil (email, senha, idUsuario) VALUES (:email, :senha, :idUsuario)";
                    $senha = password_hash($this->_senha, PASSWORD_ARGON2I);

                
                    $stmt = $this->_conn->prepare($sql);
                    $stmt->bindParam(":email", $this->_email);
                    $stmt->bindParam(":senha", $senha);
                    $stmt->bindParam(":idUsuario", $result["idUsuario"]);
                    $stmt->execute();
                    return gerarResposta("Usuário cadastrado com sucesso");
                } else {
                
                    $sql = "INSERT INTO tblusuario (nome, cpf, telefone) VALUES (:nome, :cpf, :telefone)";
                    $stmt = $this->_conn->prepare($sql);
                    $stmt->bindParam(":nome", $this->_nome);
                    $stmt->bindParam(":cpf", $this->_cpf);
                    $stmt->bindParam(":telefone", $this->_telefone);
                    $stmt->execute();
                    $idUsuario = $this->_conn->lastInsertId();
        
                    $sql = "INSERT INTO tblperfil (email, senha, idUsuario) VALUES (:email, :senha, :idUsuario)";
                    $senha = password_hash($this->_senha, PASSWORD_ARGON2I);
                    $stmt = $this->_conn->prepare($sql);
                    $stmt->bindParam(":email", $this->_email);
                    $stmt->bindParam(":senha", $senha);
                    $stmt->bindParam(":idUsuario", $idUsuario);
                    $stmt->execute();
                    return gerarResposta("Usuário cadastrado com sucesso");
                
                }
            } catch (PDOException $error) {
                return gerarResposta($error->getMessage(), 'erro');
            }

        }
    }

    function update() {

        $sqlPerfil = "SELECT * FROM tblperfil WHERE idUsuario = :idUsuario";

        $stm = $this->_conn->prepare($sqlPerfil);
        $stm->bindParam(":idUsuario", $this->_idUsuario);
        $stm->execute();
        $perfil = $stm->fetch(PDO::FETCH_ASSOC);

        $sqlUpdatePerfil = "UPDATE tblperfil SET email = :email, senha = :senha WHERE idUsuario = :idUsuario";

        $stm = $this->_conn->prepare($sqlUpdatePerfil);
        $stm->bindParam(":email", $this->_email);
        $stm->bindParam(":senha", $this->_senha);
        $stm->bindParam(":idUsuario", $this->_idUsuario);

        $stm->execute();

        if($perfil["foto"] !== null) {
            unlink("../usuario/uploads/" . $perfil["foto"]);
        }

        $sqlDeletarImagemExistente = "UPDATE tblperfil SET foto = NULL WHERE idUsuario = :idUsuario";

        $statementDelecaoImagem = $this->_conn->prepare($sqlDeletarImagemExistente);
        $statementDelecaoImagem->bindParam(":idUsuario", $this->_idUsuario);
        $statementDelecaoImagem->execute();
        
        $imagem = $_FILES["foto"];
        $extensao = pathinfo($imagem['name'], PATHINFO_EXTENSION);
        $this->_nomeArquivo = md5(microtime()) . ".$extensao";
        $insercaoImagem = move_uploaded_file($imagem["tmp_name"], "../usuario/uploads/$this->_nomeArquivo");
        
        $sqlNovaImagem = "UPDATE tblperfil SET foto = :foto WHERE idUsuario = :idUsuario";

        $statementInsercaoImagem = $this->_conn->prepare($sqlNovaImagem);
        $statementInsercaoImagem->bindParam(":foto", $this->_nomeArquivo);
        $statementInsercaoImagem->bindParam(":idUsuario", $this->_idUsuario);
        $statementInsercaoImagem->execute();

        return gerarResposta("Sucesso");

    }

    function delete() {
        
        try {
            
            $sqlPerfil = "SELECT * FROM tblperfil WHERE idPerfil = :idPerfil";

            $stm = $this->_conn->prepare($sqlPerfil);
            $stm->bindParam(":idPerfil", $this->_idPerfil);
            $stm->execute();
            $perfil = $stm->fetch(PDO:: FETCH_ASSOC);
         
            if($perfil["foto"] !== null) {
                unlink("../usuario/uploads/" . $perfil["foto"]);
            }

            $sqlDelecaoPerfil = "DELETE FROM tblperfil WHERE idPerfil = :idPerfil";

            $stm = $this->_conn->prepare($sqlDelecaoPerfil);
            $stm->bindParam(":idPerfil", $this->_idPerfil);
            $stm->execute();

            return gerarResposta("Sucesso");

        } catch (PDOException $error) {
            return gerarResposta($error->getMessage(), 'erro');
        }
        
    }

    function login() {
        $sql = "SELECT * FROM tblperfil WHERE email = :email";
        $stmt = $this->_conn->prepare($sql);
        $stmt->bindParam(":email", $this->_email);
        $stmt->execute();

        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);

        if(password_verify($this->_senha, $perfil["senha"])) {
            $sql = "SELECT * FROM tblusuario WHERE idUsuario = :idUsuario";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindParam(":idUsuario", $perfil["idUsuario"]);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            return gerarResposta(JWT::encode([
                "idPerfil" => $perfil["idPerfil"],
                "email" => $perfil["email"],
                "foto" => $perfil["foto"],
                "isAdmin" => $perfil["eAdmin"],
                "usuario" => $usuario
            ], "softcake_auth"));
        } else {
            return gerarResposta("Usuário ou senha incorretos", 'erro');
        }
    }

    function verifyToken() {
        $token = str_replace("Bearer ", "", $this->_token);
        $decodedToken = JWT::decode($token, "softcake_auth", ['HS256']);
        return $decodedToken;
    }
}

?>