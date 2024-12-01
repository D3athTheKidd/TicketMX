<?php
// db.php
$servername = "localhost";
$username = "root";
$password = "";
$database = "proyecto_concierto";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Comprobar si la conexión ha fallado
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Establecer la codificación UTF-8 para la conexión
$conn->set_charset("utf8mb4");
?>
