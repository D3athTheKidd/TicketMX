<?php
include 'conexion_be.php';

$nombre = $_POST['nombre'];
$correo = $_POST['correo'];
$usuario = $_POST['usuario'];
$contrasena = $_POST['contrasena'];

$query = "INSERT INTO usuarios (nombre, correo, usuario, contrasena) VALUES ('$nombre', '$correo', '$usuario', '$contrasena')";
$ejecutar = mysqli_query($conexion, $query);

if ($ejecutar) {
    echo '<script>alert("Usuario agregado correctamente"); window.location="crud.php";</script>';
} else {
    echo '<script>alert("Error al agregar el usuario"); window.location="crud.php";</script>';
}

mysqli_close($conexion);
?>