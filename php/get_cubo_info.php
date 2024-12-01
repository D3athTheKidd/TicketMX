<?php
// Conectar a la base de datos
include('db.php');

// Obtener los datos de la tabla `informacion_cubo`
$query = "SELECT cara, descripcion FROM informacion_cubo";
$result = mysqli_query($conn, $query);

// Comprobar si la consulta fue exitosa
if ($result) {
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    // Devolver los datos en formato JSON
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'No se pudo obtener la información del cubo']);
}

// Cerrar la conexión
mysqli_close($conn);
?>
