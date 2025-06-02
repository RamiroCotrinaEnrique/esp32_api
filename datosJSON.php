<?php
include 'config/conexion.php';

header('Content-Type: application/json');

$sql = "SELECT dth11_temperatura, dth11_humedad, dth11_fecha_create FROM sensorDTH11 ORDER BY dth11_fecha_create ASC";
$result = $con->query($sql);

$fechas = [];
$temps = [];
$humedades = [];
$i = 0;

while ($row = $result->fetch_assoc()) {
    //if ($i % 30 === 0) {
        $fechas[] = $row['dth11_fecha_create'];
        $temps[] = $row['dth11_temperatura'];
        $humedades[] = $row['dth11_humedad'];
   // }
    //$i++;
}

echo json_encode([
    'fechas' => $fechas,
    'temps' => $temps,
    'humedades' => $humedades
]);
