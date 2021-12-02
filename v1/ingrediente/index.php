<?php

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Content-Type: application/json');

    include('../database/Conexao.php');
    include('../model/ModelIngrediente.php');
    include('../controller/ControllerIngrediente.php');

    $conn = new Conexao();
    $model = new ModelIngrediente($conn->getConnection());
    $controller = new ControllerIngrediente($model);

    $data = $controller->router();
    echo json_encode($data);
?>