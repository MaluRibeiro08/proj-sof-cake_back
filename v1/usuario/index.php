<?php

    header("Acess-Control-Allow-Origin: *");
    header("Acess-Control-Allow-Headers: *");
    header("Acess-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Content-Type: application/json");

    include('../database/Conexao.php');
    include('../model/ModelUsuario.php');
    include('../controller/ControllerUsuario.php');

    $conn = new Conexao();
    $model = new ModelUsuario($conn->getConnection());
    $controller = new ControllerUsuario($model);

    $dados = $controller->router();

    echo json_encode(array("status"=>"Sucess", "data"=>$dados));
