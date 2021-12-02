<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json');

include('../database/Conexao.php');
include('../model/ModelBolo.php');
include('../controller/ControllerBolo.php');

$conn = new Conexao();
$model = new ModelBolo($conn->getConnection());
$controller = new ControllerBolo($model);

$data = $controller->router();
echo json_encode($data);



?>