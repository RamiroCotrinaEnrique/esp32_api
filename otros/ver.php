<?php
// db.php - conexión a MySQL
$host = '161.132.68.39';
$user = 'user';
$pass = 'Vallejo2025';
$db = 'api';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>

<!-- index.php - mostrar datos -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Sensor DHT11</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Reporte de Temperatura y Humedad</h2>
        <div style="overflow-x: auto;">
    <canvas id="lineChart" style="min-width: 1000px;"></canvas>
</div>

</div>

<?php
include 'config/conexion.php';
$sql = "SELECT dth11_temperatura, dth11_humedad, dth11_fecha_create FROM sensorDTH11 ORDER BY dth11_fecha_create ASC";
$result = $conn->query($sql);
$fechas = [];
$temps = [];
$humedades = [];
$i = 0;
while ($row = $result->fetch_assoc()) {
    if ($i % 30 === 0) {
        $fechas[] = $row['dth11_fecha_create'];
        $temps[] = $row['dth11_temperatura'];
        $humedades[] = $row['dth11_humedad'];
    }
    $i++;
}
$conn->close();
?>

<script>
const ctx = document.getElementById('lineChart').getContext('2d');
const lineChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($fechas); ?>,
        datasets: [
            {
                label: 'Temperatura (°C)',
                data: <?php echo json_encode($temps); ?>,
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.3
            },
            {
                label: 'Humedad (%)',
                data: <?php echo json_encode($humedades); ?>,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            x: {
                ticks: {
                    maxRotation: 90,
                    minRotation: 45
                }
            },
            y: {
                beginAtZero: false
            }
        }
    }
});
</script>
</body>
</html>
