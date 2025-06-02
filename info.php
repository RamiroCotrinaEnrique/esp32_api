<?php include 'config/conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Sensor DHT11</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-10">
    <h2 class="mb-4">Reporte de Temperatura y Humedad</h2>
    <div style="overflow-x: auto;">
        <canvas id="lineChart" style="min-width: 1000px;"></canvas>
    </div>
</div>

<script>
const ctx = document.getElementById('lineChart').getContext('2d');
const lineChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [],
        datasets: [
            {
                label: 'Temperatura (°C)',
                data: [],
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.3
            },
            {
                label: 'Humedad (%)',
                data: [],
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true
    }
});

function actualizarDatos() {
    $.ajax({
        url: 'datosJSON.php', // ← archivo separado para solo devolver JSON
        method: 'GET',
        dataType: 'json',
        success: function(res) {
            lineChart.data.labels = res.fechas;
            lineChart.data.datasets[0].data = res.temps;
            lineChart.data.datasets[1].data = res.humedades;
            lineChart.update();
        },
        error: function(err) {
            console.error("Error al cargar datos:", err);
        }
    });
}

actualizarDatos(); // Carga inicial
//setInterval(actualizarDatos, 10000); // Cada 10 segundos
setInterval(actualizarDatos, 10000); // Cada 10 segundos
</script>
</body>
</html>
