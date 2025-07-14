<?php
// --- CONFIGURACIÓN DE LA BASE DE DATOS ---

define('DB_SERVER', '161.132.68.39'); // nombre o la IP de tu servidor de BD
define('DB_USERNAME', 'user');      // tu usuario de BD
define('DB_PASSWORD', 'Vallejo2025'); // tu contraseña de BD
define('DB_NAME', 'api'); // el nombre de tu base de datos

// --- Intento de conexión a la base de datos MySQL ---
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// --- Verificar la conexión ---
if($conn === false){
    // Si la conexión falla, se detiene la ejecución y se muestra un error.
    // En un entorno de producción, es mejor registrar este error en un archivo.
    die("ERROR: No se pudo conectar a la base de datos. " . mysqli_connect_error());
}

// --- Establecer el juego de caracteres a UTF-8 ---
// Esto es importante para evitar problemas con tildes y caracteres especiales.
mysqli_set_charset($conn, "utf8");

?>
