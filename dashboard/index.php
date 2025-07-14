<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard IoT en Tiempo Real</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Estilos personalizados para un look moderno */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
        }
        .card-sensor {
            background: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease-in-out;
        }
        .card-sensor:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #e9ecef;
            font-weight: 700;
            color: #343a40;
        }
        .sensor-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #0d6efd;
        }
        .sensor-icon {
            font-size: 3rem;
            color: #0d6efd;
            opacity: 0.7;
        }
        .chart-container {
            position: relative;
            height: 350px;
            width: 100%;
        }
        .last-update {
            font-size: 0.8rem;
            color: #6c757d;
        }
    </style>
</head>
<body>

    <div class="container-fluid p-4">
        <h1 class="mb-4 text-center fw-bold">Dashboard de Monitoreo IoT</h1>

        <!-- Fila para las lecturas actuales -->
        <div class="row mb-4 g-4">
            <div class="col-md-4">
                <div class="card card-sensor p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title text-muted">Temperatura</h5>
                            <p class="sensor-value mb-0" id="temp-value">--.-- °C</p>
                        </div>
                        <i class="fas fa-thermometer-half sensor-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-sensor p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title text-muted">Humedad</h5>
                            <p class="sensor-value mb-0" id="humidity-value">--.-- %</p>
                        </div>
                        <i class="fas fa-tint sensor-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-sensor p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title text-muted">Monóxido de Carbono (MQ-7)</h5>
                            <p class="sensor-value mb-0" id="gas-value">--.-- ppm</p>
                        </div>
                        <i class="fas fa-smog sensor-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fila para las gráficas -->
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card card-sensor">
                    <div class="card-header d-flex justify-content-between align-items-center">
                       <span><i class="fas fa-chart-line me-2"></i>Historial de Temperatura y Humedad</span>
                       <span class="last-update" id="update-time-dth11"></span>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="dth11Chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-sensor">
                     <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-chart-area me-2"></i>Historial de Gas (MQ-7)</span>
                        <span class="last-update" id="update-time-mq7"></span>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="mq7Chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery (necesario para AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Chart.js para las gráficas -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    $(document).ready(function() {
        // --- Configuración de la Gráfica DTH11 (Temperatura y Humedad) ---
        const ctxDTH11 = document.getElementById('dth11Chart').getContext('2d');
        const dth11Chart = new Chart(ctxDTH11, {
            type: 'line',
            data: {
                labels: [], // Las etiquetas de tiempo irán aquí
                datasets: [{
                    label: 'Temperatura (°C)',
                    data: [], // Los datos de temperatura irán aquí
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Humedad (%)',
                    data: [], // Los datos de humedad irán aquí
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: false },
                    x: { ticks: { maxRotation: 0, minRotation: 0 } }
                },
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });

        // --- Configuración de la Gráfica MQ7 (Gas) ---
        const ctxMQ7 = document.getElementById('mq7Chart').getContext('2d');
        const mq7Chart = new Chart(ctxMQ7, {
            type: 'line',
            data: {
                labels: [], // Las etiquetas de tiempo irán aquí
                datasets: [{
                    label: 'Nivel de Gas (ppm)',
                    data: [], // Los datos de gas irán aquí
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // --- Función para obtener y actualizar los datos ---
        function updateData() {
            $.ajax({
                url: 'get_data.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    // --- Actualizar valores actuales ---
                    if (response.latest_dth11) {
                        $('#temp-value').text(response.latest_dth11.dth11_temperatura + ' °C');
                        $('#humidity-value').text(response.latest_dth11.dth11_humedad + ' %');
                    }
                    if (response.latest_mq7) {
                        $('#gas-value').text(response.latest_mq7.mq7_gas + ' ppm');
                    }
                    
                    const now = new Date();
                    const timeString = now.toLocaleTimeString('es-ES');
                    $('#update-time-dth11').text('Última act: ' + timeString);
                    $('#update-time-mq7').text('Última act: ' + timeString);


                    // --- Actualizar gráfica DTH11 ---
                    if (response.history_dth11) {
                        dth11Chart.data.labels = response.history_dth11.labels;
                        dth11Chart.data.datasets[0].data = response.history_dth11.temperatures;
                        dth11Chart.data.datasets[1].data = response.history_dth11.humidities;
                        dth11Chart.update();
                    }

                    // --- Actualizar gráfica MQ7 ---
                    if (response.history_mq7) {
                        mq7Chart.data.labels = response.history_mq7.labels;
                        mq7Chart.data.datasets[0].data = response.history_mq7.gas_levels;
                        mq7Chart.update();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error al obtener datos:", error);
                }
            });
        }

        // --- Llamada inicial y programación del intervalo ---
        updateData(); // Llama a la función la primera vez para cargar datos inmediatamente
        setInterval(updateData, 10000); // Actualiza los datos cada 10 segundos
    });
    </script>
</body>
</html>
