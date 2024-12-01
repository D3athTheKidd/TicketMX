<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/concierto/php/database.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // Obtener el artículo existente
    $sql = "SELECT nombre, precio, stock, imagen FROM articulos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $articulo = $result->fetch_assoc();

    if (!$articulo) {
        $_SESSION['msg'] = "Artículo no encontrado.";
        $_SESSION['color'] = "danger";
        header("Location: index.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];
        $stock = $_POST['stock'];
        $imagen = $articulo['imagen']; // Mantener la imagen existente

        // Manejo de la carga de nuevas imágenes
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $nuevaImagen = $_FILES['imagen']['name'];
            $rutaImagen = $_SERVER['DOCUMENT_ROOT'] . '/concierto/imagenes/' . basename($nuevaImagen);

            // Mover el archivo a la carpeta de imágenes
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagen)) {
                $imagen = $nuevaImagen; // Actualizar la imagen si se subió una nueva
            } else {
                $_SESSION['msg'] = "Error al cargar la nueva imagen.";
                $_SESSION['color'] = "danger";
            }
        }

        // Actualizar en la base de datos
        $sql = "UPDATE articulos SET nombre = ?, precio = ?, stock = ?, imagen = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdssi", $nombre, $precio, $stock, $imagen, $id);

        if ($stmt->execute()) {
            $_SESSION['msg'] = "Artículo actualizado exitosamente.";
            $_SESSION['color'] = "success";
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['msg'] = "Error al actualizar artículo: " . $stmt->error;
            $_SESSION['color'] = "danger";
        }
    }
} else {
    $_SESSION['msg'] = "ID de artículo no proporcionado.";
    $_SESSION['color'] = "danger";
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Artículo</title>
    <link href="/concierto/assets/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-3">
        <h2>Editar Artículo</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($articulo['nombre']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="precio" class="form-label">Precio</label>
                <input type="number" class="form-control" id="precio" name="precio" value="<?= htmlspecialchars($articulo['precio']); ?>" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" class="form-control" id="stock" name="stock" value="<?= htmlspecialchars($articulo['stock']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen</label>
                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                <small>Dejar vacío si no desea cambiar la imagen.</small>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Artículo</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script src="/concierto/assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
