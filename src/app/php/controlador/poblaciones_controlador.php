<?php
    require '../modelo/poblaciones_modelo.php';

    $poblacionesModelo = new Poblaciones_modelo();
    
    header('Access-Control-Allow-Origin:*');
    header('Content-Type: application/json; charset=utf-8');

    $data = json_decode(file_get_contents('php://input'), true);


    if($poblacionesModelo->altaPoblaciones($data["nombre"],$data["imagen"], $data["descripcion"])){
        echo json_encode('Datos enviados');
    }
    else{
        echo json_encode('Error al enviar los datos');
    } 

