<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<?php
include 'conex.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM articulos WHERE id=$id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $imagen = $_POST['imagen'];

    $sql = "UPDATE articulos SET nombre='$nombre', precio='$precio', stock='$stock', imagen='$imagen' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Artículo actualizado con éxito.";
    } else {
        echo "Error al actualizar: " . $conn->error;
    }

    $conn->close();
}
?>

<form method="post" action="editar.php?id=<?php echo $id; ?>">
    <input type="text" name="nombre" value="<?php echo $row['nombre']; ?>" required>
    <input type="number" step="0.01" name="precio" value="<?php echo $row['precio']; ?>" required>
    <input type="number" name="stock" value="<?php echo $row['stock']; ?>" required>
    <input type="text" name="imagen" value="<?php echo $row['imagen']; ?>" required>
    <button type="submit">Actualizar Artículo</button>
</form>
