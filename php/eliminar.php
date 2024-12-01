<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<?php
include 'conex.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM articulos WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Artículo eliminado con éxito.";
    } else {
        echo "Error al eliminar: " . $conn->error;
    }

    $conn->close();
}
?>
