<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/concierto/php/database.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // Primero, obtener el nombre de la imagen para poder eliminarla del servidor
    $sql = "SELECT imagen FROM articulos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $articulo = $result->fetch_assoc();

    if ($articulo) {
        // Eliminar el artículo de la base de datos
        $sql = "DELETE FROM articulos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Eliminar la imagen del servidor
            $rutaImagen = $_SERVER['DOCUMENT_ROOT'] . '/concierto/imagenes/' . $articulo['imagen'];
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            }

            $_SESSION['msg'] = "Artículo eliminado exitosamente.";
            $_SESSION['color'] = "success";
        } else {
            $_SESSION['msg'] = "Error al eliminar artículo: " . $stmt->error;
            $_SESSION['color'] = "danger";
        }
    } else {
        $_SESSION['msg'] = "Artículo no encontrado.";
        $_SESSION['color'] = "danger";
    }
} else {
    $_SESSION['msg'] = "ID de artículo no proporcionado.";
    $_SESSION['color'] = "danger";
}

header("Location: index.php");
exit();
