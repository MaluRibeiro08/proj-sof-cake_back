<?php

require_once '../../vendor/autoload.php';
use Firebase\JWT\JWT;

class ModelAuth {
    private $_conn;
    private $_cpf;
    private $_email;
    private $_senha;
    private $_token;
    private $_foto;

    function __construct($conn) {
        $json = file_get_contents("php://input");
        $dadosUsuario = json_decode($json);

        $this->_conn = $conn;
        $this->_cpf = $_REQUEST["cpf"] ?? $dadosUsuario->cpf ?? null;
        $this->_email = $_REQUEST["email"] ?? $dadosUsuario->email ?? null;
        $this->_senha = $_REQUEST["senha"] ?? $dadosUsuario->senha ?? null;
        $this->_token = $_SERVER["HTTP_AUTHORIZATION"];
        $this->_foto = "https://images.unsplash.com/photo-1638473832156-742c025bc377?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=687&q=80";

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
    
            if($result) {
                $sql = "INSERT INTO tblperfil (email, senha, foto, idUsuario) VALUES (:email, :senha, :foto, :idUsuario)";
                $senha = password_hash($this->_senha, PASSWORD_ARGON2I);
                $stmt = $this->_conn->prepare($sql);
                $stmt->bindParam(":email", $this->_email);
                $stmt->bindParam(":senha", $senha);
                $stmt->bindParam(":foto", $this->_foto);
                $stmt->bindParam(":idUsuario", $result["idUsuario"]);
                $stmt->execute();
                return gerarResposta("Usuário cadastrado com sucesso");
            } else {
                $sql = "INSERT INTO tblusuario (cpf) VALUES (:cpf)";
                $stmt = $this->_conn->prepare($sql);
                $stmt->bindParam(":cpf", $this->_cpf);
                $stmt->execute();
                $idUsuario = $this->_conn->lastInsertId();
    
                $sql = "INSERT INTO tblperfil (email, senha, foto, idUsuario) VALUES (:email, :senha, :foto, :idUsuario)";
                $senha = password_hash($this->_senha, PASSWORD_ARGON2I);
                $stmt = $this->_conn->prepare($sql);
                $stmt->bindParam(":email", $this->_email);
                $stmt->bindParam(":senha", $senha);
                $stmt->bindParam(":foto", $this->_foto);
                $stmt->bindParam(":idUsuario", $idUsuario);
                $stmt->execute();
                return gerarResposta("Usuário cadastrado com sucesso");
            }
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