<?php
include 'config/conexion.php'; 

if ($conexion->connect_error) {
    die("Connection no establecida: " . $conexion->connect_error);
}

header("Content-Type: application/json");
$metodo = $_SERVER['REQUEST_METHOD'];

$ruta = isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'/';

$buscarId = explode('/', $ruta);
$id = ($ruta!=='/')?end($buscarId):null;

//print_r($metodo);
$tabla =null;

switch ($metodo) {
    case 'GET':   
        //echo "Consulta registro GET";
        $tabla = "sensorDTH11";
        metodoGET($conexion,$tabla);
        break;

    case 'POST':
        //echo "Consulta registro POST";
        $tabla = "sensorDTH11";
        metodoPOST($conexion,$tabla);
        break;

    case 'PUT':
        echo "Consulta registro PUT";
        break;

    case 'DELETE':
        //echo "Consulta registro DELETE";
        $tabla = "sensorDTH11";
        metodoDELETE($conexion,$id,$tabla);
        break;

    default:
        echo "MÉTODO NO PERMITIDO";
        break;
}

function metodoGET($conexion,$tabla){
    $sql = "SELECT * FROM ". $tabla;
    $resultado = $conexion->query($sql);

    if ($resultado) {
        $datos = array();
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }
        echo json_encode($datos);
    }
}

function metodoPOST($conexion,$tabla){
    $dato = json_decode(file_get_contents('php://input'),true);
    $temperatura = $dato['temperatura'];
    $humedad = $dato['humedad'];
    //print_r($temperatura);
    //print_r($humedad);
    $sql = "INSERT INTO ". $tabla . " (dth11_temperatura, dth11_humedad) VALUES ('$temperatura', '$humedad')";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        $dato['id'] = $conexion->insert_id;
        echo json_encode($dato);
    }else{
        echo json_encode(array('error'=>'Error al registrar datos'));
    }

}

function metodoDELETE($conexion,$id,$tabla){
    echo "El ide s : " .$id;
}
