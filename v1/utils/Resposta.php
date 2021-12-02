<?php
    function gerarResposta($mensagem, $tipo = '') {
        if($tipo == 'erro') {
            return [
                "status" => "error",
                "message" => $mensagem
            ];
        } else {
            return [
                "status" => "success",
                "message" => $mensagem
            ];
        }
    }

?>