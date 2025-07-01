<?php

include 'config/conexion.php';

if ($con) {
    echo "¡Conexión con base de datos exitosa!<br>";
 
} else {
    echo "¡Falla! Conexión con base de datos.";
}
// Consulta SQL
$sql = "SELECT * FROM sensorDTH11 ORDER BY dth11_id DESC";
$result = $conn->query($sql);
?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lecturas del Sensor DHT11</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Lecturas del Sensor DHT11</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Temperatura (°C)</th>
                        <th>Humedad (%)</th>
                        <th>Fecha de Registro</th>
                        <th>Última Actualización</th>
                        <th>Fecha de Eliminación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['dth11_id']) ?></td>
                                <td><?= htmlspecialchars($row['dth11_temperatura']) ?></td>
                                <td><?= htmlspecialchars($row['dth11_humedad']) ?></td>
                                <td><?= htmlspecialchars($row['dth11_fecha_create']) ?></td>
                                <td><?= $row['dth11_fecha_update'] ?? '--' ?></td>
                                <td><?= $row['dth11_fecha_delete'] ?? '--' ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No hay datos disponibles.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
