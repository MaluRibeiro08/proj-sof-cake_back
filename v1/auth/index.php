<?php

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Content-Type: application/json");

    include('../database/Conexao.php');
    include('../model/ModelAuth.php');
    include('../controller/ControllerAuth.php');

    $conn = new Conexao();
    $model = new ModelAuth($conn->getConnection());
    $controller = new ControllerAuth($model);

    $dados = $controller->router();

    echo json_encode($dados);
