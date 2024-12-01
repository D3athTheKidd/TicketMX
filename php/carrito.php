<?php
include 'db.php'; // Incluye el archivo de conexión a la base de datos
session_start(); // Inicia la sesión para usar el carrito

// Manejo del formulario de agregar boletos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['boleto_id'])) {
    // Verificar si el carrito ya existe en la sesión
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Agregar el boleto al carrito
    $articulo_id = intval($_POST['articulo_id']); // Asegúrate de que este sea el ID correcto
    $nombre = $_POST['nombre'];
    $precio = floatval($_POST['precio']);
    $imagen = $_POST['imagen'];
    $cantidad = intval($_POST['cantidad']);

    $_SESSION['carrito'][] = [
        'articulo_id' => $articulo_id,
        'nombre' => $nombre,
        'precio' => $precio,
        'imagen' => $imagen,
        'cantidad' => $cantidad
    ];
}

// Manejo de la eliminación de boletos del carrito
if (isset($_POST['eliminar_boleto'])) {
    $articulo_id = intval($_POST['eliminar_boleto']);
    foreach ($_SESSION['carrito'] as $key => $item) {
        if ($item['articulo_id'] == $articulo_id) {
            unset($_SESSION['carrito'][$key]);
            break;
        }
    }
    $_SESSION['carrito'] = array_values($_SESSION['carrito']); // Reindexar el array
}

// Calcular el total a pagar
$total = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }
}

// Manejo del procedimiento de pago
if (isset($_POST['proceder_pago'])) {
    // Llamar a la función para verificar el stock
    if (verificarStock($conn, $_SESSION['carrito'])) {
        // Llamar a la función para actualizar el stock
        actualizarStock($conn, $_SESSION['carrito']);

        // Llamar a la función para registrar la venta
        registrarVenta($conn, $_SESSION['carrito']);

        // Redirigir a checkout
        header("Location: checkout.php");
        exit();
    } else {
        echo "No hay suficiente stock para uno de los boletos en el carrito.";
    }
}

// Función para verificar el stock
function verificarStock($conn, $carrito) {
    foreach ($carrito as $item) {
        $articulo_id = $item['articulo_id'];
        $cantidad = $item['cantidad'];

        // Consultar el stock disponible en la tabla articulos usando el articulo_id
        $sql = "SELECT a.stock 
                FROM articulos a
                JOIN boletos b ON a.id = b.articulo_id 
                WHERE b.articulo_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $articulo_id);
        $stmt->execute();
        $stmt->bind_result($stock);
        $stmt->fetch();
        $stmt->close();

        // Verificar si hay suficiente stock
        if ($cantidad > $stock) {
            return false; // No hay suficiente stock
        }
    }
    return true; // Hay suficiente stock para todos
}

// Función para disminuir el stock
function actualizarStock($conn, $carrito) {
    foreach ($carrito as $item) {
        $articulo_id = $item['articulo_id'];
        $cantidad = $item['cantidad'];
        
        // Actualizar el stock en la tabla articulos
        $sql = "UPDATE articulos SET stock = stock - ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $cantidad, $articulo_id);

        if (!$stmt->execute()) {
            echo "Error al actualizar el stock para el artículo ID $articulo_id: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Función para registrar la venta en la tabla ventas
function registrarVenta($conn, $carrito) {
    foreach ($carrito as $item) {
        $articulo_id = $item['articulo_id'];
        $cantidad = $item['cantidad'];
        $precio = $item['precio'];
        $fecha = date('Y-m-d H:i:s'); // Obtén la fecha actual

        // Insertar la venta en la tabla ventas
        $sql = "INSERT INTO ventas (articulo_id, cantidad, precio, fecha) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iids", $articulo_id, $cantidad, $precio, $fecha);

        if (!$stmt->execute()) {
            echo "Error al registrar la venta para el artículo ID $articulo_id: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-custom {
            background-color: #0056b3;
        }
        .navbar-custom .navbar-brand, 
        .navbar-custom .nav-link {
            color: #fff;
        }
        .hero-section {
            background-color: #0056b3;
            color: white;
            text-align: center;
            padding: 40px 0;
        }
        .price {
            color: #dc3545;
            font-weight: bold;
        }
        footer {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
        .card {
            margin-bottom: 20px;
            border: 1px solid #0056b3;
            border-radius: 8px;
        }
        .card img {
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">TicketsMX</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="crud.php">Conciertos</a></li>
                </ul>
                <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Buscar..." aria-label="Search">
                    <button class="btn btn-outline-light" type="submit">Buscar</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="hero-section">
        <h1>Carrito de Compras</h1>
    </div>

    <div class="container my-5">
        <?php if (!empty($_SESSION['carrito'])): ?>
            <div class="row">
                <?php foreach ($_SESSION['carrito'] as $item): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="../assets/uploads/<?php echo $item['imagen']; ?>" class="card-img-top" alt="Imagen del Boleto">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $item['nombre']; ?></h5>
                                <p class="price">$<?php echo number_format($item['precio'], 2); ?></p>
                                <p>Cantidad: <?php echo $item['cantidad']; ?></p>
                                <form action="" method="post">
                                    <input type="hidden" name="eliminar_boleto" value="<?php echo $item['articulo_id']; ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este boleto del carrito?')">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-end">
                <h3>Total a Pagar: $<?php echo number_format($total, 2); ?></h3>
                <a href="crud.php" class="btn btn-primary">Regresar</a>
                <form action="" method="post" style="display:inline;">
                    <button type="submit" name="proceder_pago" class="btn btn-success">Proceder al Pago</button>
                </form>
            </div>
        <?php else: ?>
            <p>No hay boletos en el carrito. <a href="crud.php" class="btn btn-primary">Ver Boletos</a></p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> TicketsMX</p>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
