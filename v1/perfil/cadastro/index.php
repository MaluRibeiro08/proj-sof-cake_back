<?php

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Content-Type: application/json");

    include('../../database/Conexao.php');
    include('../../model/ModelPerfil.php');
    include('../../controller/ControllerPerfil.php');

    $conn = new Conexao();
    $model = new ModelPerfil($conn->getConnection());
    $controller = new ControllerPerfil($model, 'cadastro');

    $dados = $controller->router();

    echo json_encode($dados);
