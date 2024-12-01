<?php
session_start();
require 'config/database.php';

$nombre = $conn->real_escape_string($_POST['nombre']);
$precio = $conn->real_escape_string($_POST['precio']);
$stock = $conn->real_escape_string($_POST['stock']);

// Subir la imagen
if ($_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
    $dir = "imagenes/";
    $imagen = $dir . $conn->insert_id . '.jpg'; // Nombrar imagen con el ID del concierto

    if (!file_exists($dir)) {
        mkdir($dir, 0777);
    }

    move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen);
}

// Insertar datos del concierto
$sql = "INSERT INTO conciertos (nombre, precio, stock, imagen) VALUES ('$nombre', '$precio', '$stock', '$imagen')";
if ($conn->query($sql)) {
    $_SESSION['color'] = "success";
    $_SESSION['msg'] = "Concierto guardado exitosamente";
} else {
    $_SESSION['color'] = "danger";
    $_SESSION['msg'] = "Error al guardar concierto";
}

header("Location: index.php");
?>
