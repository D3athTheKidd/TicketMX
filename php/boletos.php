<?php
include 'db.php'; // Incluye el archivo de conexión a la base de datos
session_start(); // Inicia la sesión para usar el carrito

// Obtén el id del artículo desde la URL
$articulo_id = isset($_GET['articulo_id']) ? intval($_GET['articulo_id']) : null;

// Manejo del formulario de agregar boletos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si 'boletos' está presente en POST
    if (isset($_POST['boletos'])) {
        $boletosData = $_POST['boletos']; // Obtener datos de boletos desde el formulario
        foreach ($boletosData as $boleto) {
            $nombre = $boleto['nombre'];
            $precio = $boleto['precio'];
            $articulo_id = intval($boleto['articulo_id']); // Obtener el id del artículo

            // Primero, obtenemos el stock actual del artículo
            $stock_query = "SELECT stock FROM articulos WHERE id = $articulo_id";
            $stock_result = $conn->query($stock_query);
            $articulo = $stock_result->fetch_assoc();

            if ($articulo && $articulo['stock'] > 0) {
                // Consulta para insertar un nuevo boleto
                $query = "INSERT INTO boletos (nombre, precio, articulo_id) VALUES ('$nombre', '$precio', '$articulo_id')";
                if ($conn->query($query) === TRUE) {
                    // Reducir el stock del artículo
                    $new_stock = $articulo['stock'] - 1; // Disminuir en 1
                    $update_stock_query = "UPDATE articulos SET stock = $new_stock WHERE id = $articulo_id";
                    $conn->query($update_stock_query);
                    
                    echo "<div class='alert alert-success'>Boleto '$nombre' agregado con éxito. Stock actualizado.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error al agregar el boleto: " . $conn->error . "</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>No hay suficiente stock para agregar el boleto '$nombre'.</div>";
            }
        }
    }

    // Manejo de la eliminación de boletos
    if (isset($_POST['eliminar_boleto'])) {
        $boleto_id = intval($_POST['eliminar_boleto']);
        $query = "DELETE FROM boletos WHERE id = $boleto_id";
        if ($conn->query($query) === TRUE) {
            echo "<div class='alert alert-success'>Boleto eliminado con éxito.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al eliminar el boleto: " . $conn->error . "</div>";
        }
    }
}

// Consulta para obtener los boletos del artículo seleccionado
if ($articulo_id !== null) {
    $query = "SELECT * FROM boletos WHERE articulo_id = $articulo_id";
    $boletos = $conn->query($query);
} else {
    $boletos = []; // Inicializar si no hay artículo seleccionado
}

// Consulta para obtener la información del artículo seleccionado
$articulo_query = "SELECT * FROM articulos WHERE id = $articulo_id";
$articulo_result = $conn->query($articulo_query);
$articulo = $articulo_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boletos para Artículo</title>
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
            background-color: #f8f9fa;
            text-align: center;
            padding: 40px 0;
            position: relative;
        }
        .hero-section img {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
        }
        .hero-section .hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
        }
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.05);
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
                    <li class="nav-item">
                        <button class="nav-link btn" id="toggle-form-button" data-bs-toggle="modal" data-bs-target="#agregarBoletoModal">Agregar</button>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="carrito.php">Carrito de compras</a></li>
                </ul>
                <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Buscar..." aria-label="Search">
                    <button class="btn btn-outline-light" type="submit">Buscar</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="hero-section">
        <img src="../assets/images/edc.jpg" alt="Evento Principal">
        <div class="hero-content">
            <h1>Boletos para Artículo: <?php echo $articulo ? $articulo['nombre'] : 'Artículo no encontrado'; ?></h1>
            <a href="crud.php" class="btn btn-primary">TicketsMX</a>
        </div>
    </div>

    <div class="container my-5">
        <?php if ($articulo_id !== null && $articulo): ?>
            <div class="row mb-3">
                <div class="col-md-6">
                    <img src="../assets/uploads/<?php echo $articulo['imagen']; ?>" class="img-fluid" alt="Imagen del artículo">
                </div>
                <div class="col-md-6">
                    <h2><?php echo $articulo['nombre']; ?></h2>
                    <p><?php echo $articulo['descripcion']; ?></p>
                    <p class="price">$<?php echo number_format($articulo['precio'], 2); ?></p>
                    <p class="stock">Stock: <?php echo $articulo['stock']; ?></p>
                </div>
            </div>

            <h2>Boletos Disponibles</h2>
            <div class="row">
                <?php if ($boletos && $boletos->num_rows > 0): ?>
                    <?php while ($boleto = $boletos->fetch_assoc()): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <img src="../assets/uploads/<?php echo $articulo['imagen']; ?>" class="card-img-top" alt="Imagen del Boleto">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $boleto['nombre']; ?></h5>
                                    <p class="price">$<?php echo number_format($boleto['precio'], 2); ?></p>
                                    <form action="carrito.php" method="post">
                                        <input type="hidden" name="boleto_id" value="<?php echo $boleto['id']; ?>">
                                        <input type="hidden" name="nombre" value="<?php echo $boleto['nombre']; ?>">
                                        <input type="hidden" name="precio" value="<?php echo $boleto['precio']; ?>">
                                        <input type="hidden" name="imagen" value="<?php echo $articulo['imagen']; ?>">
                                        <input type="hidden" name="articulo_id" value="<?php echo $articulo_id; ?>"> <!-- ID del artículo -->
                                        <div class="mb-3">
                                            <label for="cantidad">Cantidad:</label>
                                            <input type="number" name="cantidad" class="form-control" value="1" min="1" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Agregar al carrito</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No hay boletos disponibles para este artículo.</p>
                <?php endif; ?>
            </div>

            <!-- Modal para agregar boleto -->
            <div class="modal fade" id="agregarBoletoModal" tabindex="-1" aria-labelledby="agregarBoletoModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="agregarBoletoModalLabel">Agregar Boleto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre del Boleto</label>
                                    <input type="text" class="form-control" id="nombre" name="boletos[0][nombre]" required>
                                </div>
                                <div class="mb-3">
                                    <label for="precio" class="form-label">Precio</label>
                                    <input type="number" class="form-control" id="precio" name="boletos[0][precio]" required>
                                </div>
                                <input type="hidden" name="boletos[0][articulo_id]" value="<?php echo $articulo_id; ?>">
                                <button type="submit" class="btn btn-primary">Agregar Boleto</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <p>Artículo no encontrado.</p>
        <?php endif; ?>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2024 TicketsMX. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
