<?php
header("Content-Type: application/json");
require_once 'conexion.php'; // Archivo con la conexión a la base de datos

// Permitir solicitudes desde cualquier origen (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Obtener los datos del cuerpo de la solicitud en formato JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validar que los datos requeridos estén presentes
if (!isset($data['temperatura']) || !isset($data['humedad']) || !isset($data['gas'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

try {
    // Iniciar transacción para asegurar que ambas inserciones se completen
    $conn->beginTransaction();

    // Insertar datos en la tabla sensorDTH11
    $stmtDHT11 = $conn->prepare("
        INSERT INTO sensorDTH11 (
            dth11_temperatura, 
            dth11_humedad,
            dth11_fecha_update
        ) VALUES (
            :temperatura, 
            :humedad,
            NOW()
        )
    ");
    
    $stmtDHT11->execute([
        ':temperatura' => $data['temperatura'],
        ':humedad' => $data['humedad']
    ]);

    // Insertar datos en la tabla sensorMQ7
    $stmtMQ7 = $conn->prepare("
        INSERT INTO sensorMQ7 (
            mq7_gas,
            mq7_fecha_update
        ) VALUES (
            :gas,
            NOW()
        )
    ");
    
    $stmtMQ7->execute([
        ':gas' => $data['gas']
    ]);

    // Confirmar la transacción
    $conn->commit();

    // Respuesta exitosa
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Datos guardados correctamente',
        'dht11_id' => $conn->lastInsertId()
    ]);

} catch (PDOException $e) {
    // Revertir la transacción en caso de error
    $conn->rollBack();
    
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al guardar los datos',
        'details' => $e->getMessage()
    ]);
}
?>