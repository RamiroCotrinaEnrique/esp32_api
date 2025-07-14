<?php

// Incluir el archivo de conexión a la base de datos
require_once 'config.php';

// Establecer la cabecera para indicar que la respuesta es en formato JSON
header('Content-Type: application/json');

// Crear un array para almacenar todos los datos que se enviarán
$data = [
    'latest_dth11' => null,
    'latest_mq7' => null,
    'history_dth11' => null,
    'history_mq7' => null
];

// --- 1. Obtener la última lectura del sensor DTH11 ---
$sql_latest_dth11 = "SELECT dth11_temperatura, dth11_humedad FROM sensorDTH11 ORDER BY dth11_fecha_create DESC LIMIT 1";
if ($result = mysqli_query($conn, $sql_latest_dth11)) {
    if (mysqli_num_rows($result) > 0) {
        $data['latest_dth11'] = mysqli_fetch_assoc($result);
    }
}

// --- 2. Obtener la última lectura del sensor MQ7 ---
$sql_latest_mq7 = "SELECT mq7_gas FROM sensorMQ7 ORDER BY mq7_fecha_create DESC LIMIT 1";
if ($result = mysqli_query($conn, $sql_latest_mq7)) {
    if (mysqli_num_rows($result) > 0) {
        $data['latest_mq7'] = mysqli_fetch_assoc($result);
    }
}

// --- 3. Obtener el historial para la gráfica del DTH11 (últimos 20 registros) ---
// La subconsulta obtiene los últimos 20 y la consulta externa los ordena de forma ascendente para la gráfica.
$sql_history_dth11 = "
    SELECT 
        dth11_temperatura, 
        dth11_humedad, 
        DATE_FORMAT(dth11_fecha_create, '%H:%i:%s') as label 
    FROM 
        (SELECT * FROM sensorDTH11 ORDER BY dth11_fecha_create DESC LIMIT 20) sub 
    ORDER BY 
        dth11_fecha_create ASC
";

if ($result = mysqli_query($conn, $sql_history_dth11)) {
    $history_dth11 = [
        'labels' => [],
        'temperatures' => [],
        'humidities' => []
    ];
    while ($row = mysqli_fetch_assoc($result)) {
        $history_dth11['labels'][] = $row['label'];
        $history_dth11['temperatures'][] = $row['dth11_temperatura'];
        $history_dth11['humidities'][] = $row['dth11_humedad'];
    }
    $data['history_dth11'] = $history_dth11;
}

// --- 4. Obtener el historial para la gráfica del MQ7 (últimos 20 registros) ---
$sql_history_mq7 = "
    SELECT 
        mq7_gas, 
        DATE_FORMAT(mq7_fecha_create, '%H:%i:%s') as label 
    FROM 
        (SELECT * FROM sensorMQ7 ORDER BY mq7_fecha_create DESC LIMIT 20) sub 
    ORDER BY 
        mq7_fecha_create ASC
";

if ($result = mysqli_query($conn, $sql_history_mq7)) {
    $history_mq7 = [
        'labels' => [],
        'gas_levels' => []
    ];
    while ($row = mysqli_fetch_assoc($result)) {
        $history_mq7['labels'][] = $row['label'];
        $history_mq7['gas_levels'][] = $row['mq7_gas'];
    }
    $data['history_mq7'] = $history_mq7;
}

// --- Cerrar la conexión a la base de datos ---
mysqli_close($conn);

// --- Enviar los datos codificados en JSON como respuesta ---
echo json_encode($data);

?>
