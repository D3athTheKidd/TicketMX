<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<?php
include 'conex.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $imagen = $_POST['imagen'];

    $sql = "INSERT INTO articulos (nombre, precio, stock, imagen) VALUES ('$nombre', '$precio', '$stock', '$imagen')";

    if ($conn->query($sql) === TRUE) {
        echo "Artículo creado con éxito.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<form method="post" action="crear.php">
    <input type="text" name="nombre" placeholder="Nombre del artículo" required>
    <input type="number" step="0.01" name="precio" placeholder="Precio" required>
    <input type="number" name="stock" placeholder="Stock" required>
    <input type="text" name="imagen" placeholder="URL de la imagen" required>
    <button type="submit">Crear Artículo</button>
</form>
