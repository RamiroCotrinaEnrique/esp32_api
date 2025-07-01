<?php
include 'config/conexion.php';

if ($con) {
    echo "¡Conexión con base de datos exitosa!<br>";

    if (isset($_POST['gas']) ) {
        $gas = $_POST['gas'];  // Obtenemos el valor del campo gas

        echo "Valores de Gas en ambiente <br>";
        echo "Gas: " . $gas . "<br>"; 

        $consulta = "INSERT INTO sensorMQ7 (mq7_gas) VALUES ('$gas')";
        $resultado = mysqli_query($con, $consulta);

        if ($resultado) {
            echo "¡Registro en base de datos OK!";
        } else {
            echo "¡Falla! Registro en base de datos: " . mysqli_error($con);
        }
    } else {
        echo "Faltan datos POST (gas).";
    }
} else {
    echo "¡Falla! Conexión con base de datos.";
}
?>
