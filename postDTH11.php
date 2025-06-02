<?php
include 'config/conexion.php';

if ($con) {
    echo "¡Conexión con base de datos exitosa!<br>";

    if (isset($_POST['temperatura']) && isset($_POST['humedad'])) {
        $temperatura = $_POST['temperatura'];
        $humedad = $_POST['humedad'];

        echo "Estación meteorológica<br>";
        echo "Temperatura: " . $temperatura . "<br>";
        echo "Humedad: " . $humedad . "<br>";

        $consulta = "INSERT INTO sensorDTH11 (dth11_temperatura, dth11_humedad) VALUES ('$temperatura', '$humedad')";
        $resultado = mysqli_query($con, $consulta);

        if ($resultado) {
            echo "¡Registro en base de datos OK!";
        } else {
            echo "¡Falla! Registro en base de datos: " . mysqli_error($con);
        }
    } else {
        echo "Faltan datos POST (temperatura y/o humedad).";
    }
} else {
    echo "¡Falla! Conexión con base de datos.";
}
?>
