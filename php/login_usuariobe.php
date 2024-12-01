<?php
session_start(); // Iniciar la sesión

include 'conexion_be.php';

$correo = $_POST['correo'];
$contrasena = $_POST['contrasena'];

$validar = mysqli_query($conexion, "SELECT * FROM usuarios WHERE correo='$correo' AND contrasena='$contrasena'");

if (mysqli_num_rows($validar) > 0) {
    $_SESSION['usuario'] = $correo; 
    header("location: crud.php");
    
    exit;
} else {
    echo '
    <script>
    alert("El usuario no existe");
    window.location = "../index.php";
    </script>
    ';
    exit;
}
?>
