<?php

include 'conexion_be.php';

$nombre=$_POST['nombre'];
$correo=$_POST['correo'];
$usuario=$_POST['usuario'];
$contrasena=$_POST['contrasena'];

$query= "INSERT INTO usuarios (nombre, correo,usuario, contrasena)
        VALUES('$nombre', '$correo', '$usuario', '$contrasena')";
$ejecutar = mysqli_query($conexion, $query);

IF($ejecutar){
    echo'
    <script>
    alert("usuario guardado");
    window.location = "../index.php";
    </script>
    ';
}else{
    echo'
    <script>
    alert("Intentalo nuevamente");
    window.location = "../index.php";
    </script>
    ';


}
mysqli_close($conexion);

?>