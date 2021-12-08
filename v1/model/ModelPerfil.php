<?php

require_once '../../vendor/autoload.php';
use Firebase\JWT\JWT;

class ModelPerfil {
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
        $this->_nome = $_REQUEST["nome"] ?? $dadosUsuario->nome ?? null;
        $this->_cpf = $_REQUEST["cpf"] ?? $dadosUsuario->cpf ?? null;
        $this->_telefone = $_REQUEST["telefone"] ?? $dadosUsuario->telefone ?? null;
        $this->_email = $_REQUEST["email"] ?? $dadosUsuario->email ?? null;
        $this->_senha = $_REQUEST["senha"] ?? $dadosUsuario->senha ?? null;
        $this->_token = $_SERVER["HTTP_AUTHORIZATION"];
        $this->_foto = $_REQUEST["foto"] ?? $dadosUsuario->foto ?? null;
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

            foreach ($_FILES as $indice => $dadosImagem){
                $extensao = pathinfo($dadosImagem['name'], PATHINFO_EXTENSION);
                $this->_nomeArquivo = md5(microtime()) . ".$extensao";
                $insercaoImagem = move_uploaded_file($dadosImagem["tmp_name"], "../usuario/uploads/$this->_nomeArquivo");
            }
            try {
                if($result) {
                    $sql = "INSERT INTO tblperfil (email, senha, foto, idUsuario) VALUES (:email, :senha, :foto, :idUsuario)";
                    $senha = password_hash($this->_senha, PASSWORD_ARGON2I);

                
                    $stmt = $this->_conn->prepare($sql);
                    $stmt->bindParam(":email", $this->_email);
                    $stmt->bindParam(":senha", $senha);
                    $stmt->bindParam(":foto", $this->_nomeArquivo);
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
        
                    $sql = "INSERT INTO tblperfil (email, senha, foto, idUsuario) VALUES (:email, :senha, :foto, :idUsuario)";
                    $senha = password_hash($this->_senha, PASSWORD_ARGON2I);
                    $stmt = $this->_conn->prepare($sql);
                    $stmt->bindParam(":email", $this->_email);
                    $stmt->bindParam(":senha", $senha);
                    $stmt->bindParam(":foto", $this->_nomeArquivo);
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

        $sqlPerfil = "UPDATE tblperfil SET email = :email, senha = :senha WHERE idUsuario = :idUsuario";

        $stm = $this->_conn->prepare($sqlPerfil);
        $stm->bindParam(":email", $this->_email);
        $stm->bindParam(":senha", $this->_senha);
        $stm->bindParam(":idUsuario", $this->_idUsuario);

        $stm->execute();

        $sqlDeletarImagemExistente = "DELETE foto FROM tblperfil WHERE idUsuario = :idUsuario";

        $statementDelecaoImagem = $this->_conn->prepare($sqlDeletarImagemExistente);
        $statementDelecaoImagem->bindParam(":idUsuario", $this->_idUsuario);
        $statementDelecaoImagem->execute();

        $sqlNovaImagem = "INSERT INTO tblperfil (foto, idUsuario) VALUES (:foto, :idUsuario)";

        $statementInsercaoImagem = $this->_conn->prepare($sqlNovaImagem);
        $statementInsercaoImagem->bindParam(":foto", $this->_nomeArquivo);
        $statementInsercaoImagem->bindParam(":idUsuario", $this->_idUsuario);
        $statementInsercaoImagem->execute();
            

            // foreach ($_FILES as $indice => $dadosImagem){
                
            //     $extensao = pathinfo($dadosImagem['name'], PATHINFO_EXTENSION);
            //     $novoNomeArquivo = md5(microtime()) . ".$extensao";
            //     move_uploaded_file($dadosImagem["tmp_name"], "../bolo/uploads/$novoNomeArquivo");


            //     $sqlUpdateImagem= "INSERT INTO tblimagembolo (nomeArquivo, idBolo) VALUES (:nomeArquivo, :idBolo);";

            //     $statementImagem = $this->_conn->prepare($sqlUpdateImagem); 
            //     $statementImagem->bindParam(":nomeArquivo", $novoNomeArquivo);
            //     $statementImagem->bindParam(":idBolo", $this->_idBolo);
            //     $statementImagem->execute();
            // }

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