<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/concierto/php/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    // Manejo de la carga de imágenes
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen = $_FILES['imagen']['name'];
        $rutaImagen = $_SERVER['DOCUMENT_ROOT'] . '/concierto/imagenes/' . basename($imagen);

        // Mover el archivo a la carpeta de imágenes
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagen)) {
            // Inserción en la base de datos
            $sql = "INSERT INTO articulos (nombre, precio, stock, imagen) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdiss", $nombre, $precio, $stock, $imagen);

            if ($stmt->execute()) {
                $_SESSION['msg'] = "Artículo agregado exitosamente.";
                $_SESSION['color'] = "success";
                header("Location: index.php");
                exit();
            } else {
                $_SESSION['msg'] = "Error al agregar artículo: " . $stmt->error;
                $_SESSION['color'] = "danger";
            }
        } else {
            $_SESSION['msg'] = "Error al cargar la imagen.";
            $_SESSION['color'] = "danger";
        }
    } else {
        $_SESSION['msg'] = "Seleccione una imagen.";
        $_SESSION['color'] = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Artículo</title>
    <link href="/concierto/assets/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-3">
        <h2>Agregar Nuevo Artículo</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="precio" class="form-label">Precio</label>
                <input type="number" class="form-control" id="precio" name="precio" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" class="form-control" id="stock" name="stock" required>
            </div>
            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen</label>
                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Agregar Artículo</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="/concierto/assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
