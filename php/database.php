<?php
$servername = "localhost";  // Cambia si es necesario
$username = "root";         // Cambia si es necesario
$password = "";             // Cambia si es necesario
$dbname = "proyecto_concierto"; // Cambia por el nombre de tu base de datos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
