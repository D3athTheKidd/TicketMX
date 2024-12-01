<?php
// Incluir el archivo de conexión a la base de datos
include 'conex.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger los datos del formulario
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    
    // Manejar la subida de la imagen
    $imagen = $_FILES['imagen']['name'];
    $ruta_imagen = "uploads/" . basename($imagen);
    
    // Verifica si se subió correctamente la imagen
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_imagen)) {
        // Preparar la consulta para insertar el producto en la base de datos
        $sql = "INSERT INTO productos (nombre, precio, stock, imagen) VALUES (?, ?, ?, ?)";
        
        if ($stmt = $conexion->prepare($sql)) {
            // Asignar los parámetros a la consulta
            $stmt->bind_param('sdis', $nombre, $precio, $stock, $ruta_imagen);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                echo "Producto agregado con éxito.";
                header("Location: crud.php"); // Redirigir al CRUD
            } else {
                echo "Error al agregar el producto: " . $stmt->error;
            }

            // Cerrar la sentencia
            $stmt->close();
        } else {
            echo "Error en la preparación de la consulta: " . $conexion->error;
        }
    } else {
        echo "Error al subir la imagen.";
    }
} else {
    echo "Método no permitido.";
}
?>
