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
            $json = file_get_contents("php://input");
            $dadosBolo = json_decode($json);

            $this->_conn = $conn;
            $this->_idBolo = $_REQUEST["idBolo"] ?? $dadosBolo->idBolo ?? null;
            $this->_nomeDetalhado = $_REQUEST["nomeDetalhado"] ?? $dadosBolo->nomeDetalhado ?? null;
            $this->_nomeCard = $_REQUEST["nomeCard"] ?? $dadosBolo->nomeCard ?? null;
            $this->_precoQuilo = $_REQUEST["precoQuilo"] ?? $dadosBolo->precoQuilo ?? null;
            $this->_descricao = $_REQUEST["descricao"] ?? $dadosBolo->descricao ?? null;

            $this->_conn= $conn; 
    }

    function findOne() {
        $sqlfindOne = "SELECT tblBolo.*, tblImagemBolo.nomeArquivo, tblIngrediente.nome AS nomeIngrediente FROM tblbolo
        INNER JOIN tblImagemBolo ON tblbolo.idBolo = tblImagemBolo.idBolo
        INNER JOIN tblBoloIngrediente ON tblBolo.idBolo = tblBoloIngrediente.idBolo
        INNER JOIN tblIngrediente ON tblBoloIngrediente.idIngrediente = tblIngrediente.idIngrediente
        WHERE tblBolo.idBolo = :idBolo GROUP BY tblIngrediente.nome";

        $statement = $this->_conn->prepare($sqlfindOne); 
        $statement->bindParam(":idBolo", $this->_idBolo);
        $statement->execute(); 
        $bolos = $statement->fetchAll(\PDO::FETCH_ASSOC);


        $resultado = [];

        foreach($bolos as $bolo) {
            $bolo["nomeArquivo"] = "http://localhost/softcake/backend/v1/bolo/uploads/" . $bolo["nomeArquivo"];
            $idBolo = $bolo["idBolo"];
            $resultado[$idBolo] = array_merge(
                $bolo, 
                    [
                        "imagens"=>(
                            array_key_exists($idBolo, $resultado) && array_search($bolo["nomeArquivo"], $resultado[$idBolo]["imagens"]) !== false ?
                            $resultado[$idBolo]["imagens"] :
                            array_merge($resultado[$idBolo]["imagens"] ?? [], [$bolo["nomeArquivo"]])
                        ),
                        "ingredientes"=>(
                            array_key_exists($idBolo, $resultado) && array_search($bolo["nomeIngrediente"], $resultado[$idBolo]["ingredientes"]) !== false ?
                            $resultado[$idBolo]["ingredientes"] :
                            array_merge($resultado[$idBolo]["ingredientes"] ?? [], [$bolo["nomeIngrediente"]]) 
                        )
                    ]
                );
            unset($resultado[$bolo["idBolo"]]["nomeArquivo"]);
            unset($resultado[$bolo["idBolo"]]["nomeIngrediente"]);
        }

        return $resultado;
    }

    function findMany() {
        $sqlFindMany = "SELECT tblBolo.*, tblImagemBolo.nomeArquivo, tblIngrediente.nome AS nomeIngrediente FROM tblbolo
        INNER JOIN tblImagemBolo ON tblbolo.idBolo = tblImagemBolo.idBolo
        INNER JOIN tblBoloIngrediente ON tblBolo.idBolo = tblBoloIngrediente.idBolo
        INNER JOIN tblIngrediente ON tblBoloIngrediente.idIngrediente = tblIngrediente.idIngrediente
        GROUP BY tblIngrediente.nome";

        $statement= $this->_conn->prepare($sqlFindMany); 
        $statement->execute(); 
        $bolos = $statement->fetchAll(\PDO::FETCH_ASSOC);


        $resultado = [];

        foreach($bolos as $bolo) {
            $bolo["nomeArquivo"] = "http://localhost/softcake/backend/v1/bolo/uploads/" . $bolo["nomeArquivo"];
            $idBolo = $bolo["idBolo"];
            $resultado[$idBolo] = array_merge(
                $bolo, 
                    [
                        "imagens"=>(
                            array_key_exists($idBolo, $resultado) && array_search($bolo["nomeArquivo"], $resultado[$idBolo]["imagens"]) !== false ?
                            $resultado[$idBolo]["imagens"] :
                            array_merge($resultado[$idBolo]["imagens"] ?? [], [$bolo["nomeArquivo"]])
                        ),
                        "ingredientes"=>(
                            array_key_exists($idBolo, $resultado) && array_search($bolo["nomeIngrediente"], $resultado[$idBolo]["ingredientes"]) !== false ?
                            $resultado[$idBolo]["ingredientes"] :
                            array_merge($resultado[$idBolo]["ingredientes"] ?? [], [$bolo["nomeIngrediente"]]) 
                        )
                    ]
                );
            unset($resultado[$bolo["idBolo"]]["nomeArquivo"]);
            unset($resultado[$bolo["idBolo"]]["nomeIngrediente"]);
        }

        return $resultado;
    }

    function create() {

        try {
            $sqlCreate = "INSERT INTO tblbolo (nomeDetalhado, nomeCard, precoPorQuilo, descricao) VALUES (:nomeDetalhado, :nomeCard, :precoQuilo, :descricao)";
    
            $statement = $this->_conn->prepare($sqlCreate); 
            $statement->bindParam(":nomeDetalhado", $this->_nomeDetalhado);
            $statement->bindParam(":nomeCard", $this->_nomeCard);
            $statement->bindParam(":precoQuilo", $this->_precoQuilo);
            $statement->bindParam(":descricao", $this->_descricao);

            $statement->execute();

            $this->_idBolo = $this->_conn->lastInsertId();

            foreach ($_FILES as $indice => $dadosImagem){
                
                $extensao = pathinfo($dadosImagem['name'], PATHINFO_EXTENSION);
                $novoNomeArquivo = md5(microtime()) . ".$extensao";
                move_uploaded_file($dadosImagem["tmp_name"], "../bolo/uploads/$novoNomeArquivo");

                $sqlCreateImagem= "INSERT INTO tblimagembolo (nomeArquivo, idBolo) VALUES (:nomeArquivo, :idBolo);";

                $statementImagem = $this->_conn->prepare($sqlCreateImagem); 
                $statementImagem->bindParam(":nomeArquivo", $novoNomeArquivo);
                $statementImagem->bindParam(":idBolo", $this->_idBolo);
                $statementImagem->execute();
            }
        

            return gerarResposta("Bolo cadastrado com sucesso");

        } catch (PDOException $error) {
            return gerarResposta($error->getMessage(), 'erro');
        }
    }

    function update() {

        try {
            $sqlUpdate = "UPDATE tblbolo SET nomeDetalhado = :nomeDetalhado, nomeCard = :nomeCard, precoPorQuilo = :precoPorQuilo, descricao = :descricao WHERE idBolo = :idBolo";

            $statement = $this->_conn->prepare($sqlUpdate);
            $statement->bindParam(":nomeDetalhado", $this->_nomeDetalhado);
            $statement->bindParam(":nomeCard", $this->_nomeCard);
            $statement->bindParam(":precoPorQuilo", $this->_precoQuilo);
            $statement->bindParam(":descricao", $this->_descricao);
            $statement->bindParam(":idBolo", $this->_idBolo);
            $statement->execute();

            $sqlFotosCadastradas = "SELECT nomeArquivo FROM tblimagembolo WHERE idBolo = :idBolo";
            $statement = $this->_conn->prepare($sqlFotosCadastradas);
            $statement->bindParam(":idBolo", $this->_idBolo);
            $statement->execute();
            $arrayNomesAquivos = $statement->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($arrayNomesAquivos as $arquivo => $dadosArquivoApagar) {
                $arquivo = $dadosArquivoApagar['nomeArquivo'];
                unlink("../bolo/uploads/$arquivo");
            }

            $sqlDeletaImagensExistentes = "DELETE FROM tblimagembolo WHERE idBolo = :idBolo;";
            $statementDelecaoImagens = $this->_conn->prepare($sqlDeletaImagensExistentes);
            $statementDelecaoImagens->bindParam(":idBolo", $this->_idBolo);
            $statementDelecaoImagens->execute();
            

            foreach ($_FILES as $indice => $dadosImagem){
                
                $extensao = pathinfo($dadosImagem['name'], PATHINFO_EXTENSION);
                $novoNomeArquivo = md5(microtime()) . ".$extensao";
                move_uploaded_file($dadosImagem["tmp_name"], "../bolo/uploads/$novoNomeArquivo");


                $sqlUpdateImagem= "INSERT INTO tblimagembolo (nomeArquivo, idBolo) VALUES (:nomeArquivo, :idBolo);";

                $statementImagem = $this->_conn->prepare($sqlUpdateImagem); 
                $statementImagem->bindParam(":nomeArquivo", $novoNomeArquivo);
                $statementImagem->bindParam(":idBolo", $this->_idBolo);
                $statementImagem->execute();
            }

            return gerarResposta("Informações do bolo atualizadas com sucesso");

            
        } catch (PDOException $error) {
            return gerarResposta($error->getMessage(), 'erro');
        }
        
    }

    function delete() {
        try {

            $sqlFotosCadastradas = "SELECT nomeArquivo FROM tblimagembolo WHERE idBolo = :idBolo";
            $statement = $this->_conn->prepare($sqlFotosCadastradas);
            $statement->bindParam(":idBolo", $this->_idBolo);
            $statement->execute();
            $arrayNomesAquivos = $statement->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($arrayNomesAquivos as $arquivo => $dadosArquivoApagar) {
                $arquivo = $dadosArquivoApagar['nomeArquivo'];
                unlink("../bolo/uploads/$arquivo");
            }

            $sqlDeletaImagensExistentes = "DELETE FROM tblimagembolo WHERE idBolo = :idBolo;";
            $statementDelecaoImagens = $this->_conn->prepare($sqlDeletaImagensExistentes);
            $statementDelecaoImagens->bindParam(":idBolo", $this->_idBolo);
            $statementDelecaoImagens->execute();
            $sqlDeletaImagensExistentes = "DELETE FROM tblimagembolo WHERE idBolo = :idBolo;";
            $statementDelecaoImagens = $this->_conn->prepare($sqlDeletaImagensExistentes);
            $statementDelecaoImagens->bindParam(":idBolo", $this->_idBolo);
            $statementDelecaoImagens->execute();
            
            $sqlDeletaImagensExistentes = "DELETE FROM tblbolo WHERE idBolo = :idBolo;";
            $statementDelecaoImagens = $this->_conn->prepare($sqlDeletaImagensExistentes);
            $statementDelecaoImagens->bindParam(":idBolo", $this->_idBolo);
            $statementDelecaoImagens->execute();

            return gerarResposta("Informações do bolo apagadas com sucesso");

        } catch (PDOException $error) {
            return gerarResposta($error->getMessage(), 'erro');
        }
    }
}

?>