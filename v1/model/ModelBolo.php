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
            $this->_imagens = $_REQUEST["imagens"] ?? $dadosBolo->imagens ?? null;
            $this->_ingredientes = $_REQUEST["ingredientes"] ?? $dadosBolo->ingredientes ?? null;
            $this->_novosIngredientes = $_REQUEST["novosIngredientes"] ?? $dadosBolo->novosIngredientes ?? null;

            $this->_conn= $conn; 
    }

    function findOne() {
        $sqlfindOne = "SELECT tblBolo.*, tblImagemBolo.nomeArquivo, tblIngrediente.nome AS nomeIngrediente,
                        tblavaliacao.idavaliacao as idAvaliacao, tblAvaliacao.quantidadeEstrelas as nota,
                        tblavaliacao.comentario as comentario, tblUsuario.nome as nomeUsuario, tblPerfil.foto as fotoUsuario
            FROM tblbolo
            INNER JOIN tblImagemBolo ON tblbolo.idBolo = tblImagemBolo.idBolo
            LEFT JOIN tblBoloIngrediente ON tblBolo.idBolo = tblBoloIngrediente.idBolo
            LEFT JOIN tblIngrediente ON tblBoloIngrediente.idIngrediente = tblIngrediente.idIngrediente
            LEFT JOIN tblavaliacao on tblavaliacao.idbolo = tblbolo.idbolo
            LEFT JOIN tblUsuario on tblavaliacao.idUsuario = tblUsuario.idUsuario
            LEFT JOIN tblPerfil on tblPerfil.idUsuario = tblUsuario.idUsuario
            WHERE tblBolo.idBolo = :idBolo;";

        $statement = $this->_conn->prepare($sqlfindOne); 
        $statement->bindParam(":idBolo", $this->_idBolo);
        $statement->execute(); 
        $bolos = $statement->fetchAll(\PDO::FETCH_ASSOC);


        $resultado = [];
        $avaliacoes = [];

        foreach($bolos as $bolo) {
            $bolo["nomeArquivo"] = "http://localhost/softcake/backend/v1/bolo/uploads/" . $bolo["nomeArquivo"];
            $idBolo = $bolo["idBolo"];

            $avaliacoes[$bolo["idAvaliacao"]] = [
                "idAvaliacao" =>$bolo["idAvaliacao"],    
                "nota"=>$bolo["nota"],
                "comentario"=> $bolo["comentario"],
                "usuario"=> [
                    "nome"=>$bolo["nomeUsuario"],
                    "foto"=>$bolo["foto"] ?? null
                ]
            ];


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
                        ),
                        "avaliacoes" =>(
                            array_key_exists($idBolo, $resultado) && array_search($bolo["idAvaliacao"], $resultado[$idBolo]["avaliacoes"]) !== false ?
                            $resultado[$idBolo]["avaliacoes"] :
                            array_merge($avaliacoes) 
                        )

                    ]
                );
            unset($resultado[$bolo["idBolo"]]["nota"]);
            unset($resultado[$bolo["idBolo"]]["idNota"]);
            unset($resultado[$bolo["idBolo"]]["idAvaliacao"]);
            unset($resultado[$bolo["idBolo"]]["comentario"]);
            unset($resultado[$bolo["idBolo"]]["nomeArquivo"]);
            unset($resultado[$bolo["idBolo"]]["nomeIngrediente"]);
        }

        return $resultado;
    }

    function findMany() {
        $sqlFindMany = "SELECT tblBolo.*, tblImagemBolo.nomeArquivo, tblIngrediente.nome AS nomeIngrediente,
        tblavaliacao.quantidadeEstrelas as nota, tblavaliacao.idAvaliacao as idNota FROM tblbolo
        INNER JOIN tblImagemBolo ON tblbolo.idBolo = tblImagemBolo.idBolo
        LEFT JOIN tblBoloIngrediente ON tblBolo.idBolo = tblBoloIngrediente.idBolo
        LEFT JOIN tblIngrediente ON tblBoloIngrediente.idIngrediente = tblIngrediente.idIngrediente
        LEFT JOIN tblavaliacao on tblavaliacao.idBolo = tblbolo.idBolo";

        $statement= $this->_conn->prepare($sqlFindMany); 
        $statement->execute(); 
        $bolos = $statement->fetchAll(\PDO::FETCH_ASSOC);


        $resultado = [];
        $estrelas = [];

        /*
            estrelas: {
                1: {
                    1: 5
                }
            }
        */

        foreach($bolos as $bolo) {
            $bolo["nomeArquivo"] = "http://localhost/softcake/backend/v1/bolo/uploads/" . $bolo["nomeArquivo"];
            $idBolo = $bolo["idBolo"];
            
            if(array_key_exists($idBolo, $estrelas)) {
                !array_key_exists($bolo["idNota"], $estrelas[$idBolo]) &&
                $estrelas[$idBolo][$bolo["idNota"]] = $bolo["nota"];
            } else {
                $estrelas[$idBolo][$bolo["idNota"]] = $bolo["nota"];
            }

            $notas = 0;
            $media = 0;

            foreach ($estrelas[$idBolo] as $indice => $nota) {
                $notas += $nota;
            }
            $media = $notas / count($estrelas[$idBolo]);

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
                        ),
                        "media-avaliacoes"=>(
                            $media
                        )
                    ]
                );
            unset($resultado[$bolo["idBolo"]]["nota"]);
            unset($resultado[$bolo["idBolo"]]["idNota"]);
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


            $ingredientesCriados = [];
            foreach($this->_novosIngredientes as $ingrediente) {
                $sqlInsertIngrediente = "INSERT INTO tblIngrediente (nome) VALUES (:nome)";
                $statement = $this->_conn->prepare($sqlInsertIngrediente);
                $statement->bindValue(":nome", $ingrediente);
                $statement->execute();
                $ingredientesCriados[] = $this->_conn->lastInsertId();
            }


            $sqlIngredientes = "INSERT INTO tblBoloIngrediente (idBolo, idIngrediente) VALUES ";
            foreach(array_merge($this->_ingredientes, $ingredientesCriados) as $ingrediente) $sqlIngredientes .= "(" . $this->_idBolo . ", " . $ingrediente . "),";
            
            $sqlIngredientes = substr($sqlIngredientes, 0, -1);
            $statementIngrediente = $this->_conn->prepare($sqlIngredientes);
            $statementIngrediente->execute();


            foreach($this->_imagens as $imagem) {
                $novoNomeArquivo = md5(microtime()) . ".png";
                list($type, $data) = explode(';', $imagem);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);

                file_put_contents("../bolo/uploads/$novoNomeArquivo", $data);

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

            $sqlDeletaAvaliacoes = "DELETE FROM tblavaliacao WHERE idBolo = :idBolo;";
            $statementDelecaoAvaliacoes = $this->_conn->prepare($sqlDeletaAvaliacoes);
            $statementDelecaoAvaliacoes->bindParam(":idBolo", $this->_idBolo);
            $statementDelecaoAvaliacoes->execute();
            
            $sqlDeletaBolo = "DELETE FROM tblbolo WHERE idBolo = :idBolo;";
            $statementDelecaoBolo = $this->_conn->prepare($sqlDeletaBolo);
            $statementDelecaoBolo->bindParam(":idBolo", $this->_idBolo);
            $statementDelecaoBolo->execute();

            return gerarResposta("Informações do bolo apagadas com sucesso");

        } catch (PDOException $error) {
            return gerarResposta($error->getMessage(), 'erro');
        }
    }
}

?>