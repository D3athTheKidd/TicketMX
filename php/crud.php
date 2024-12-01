<?php
include 'db.php'; // Incluye el archivo de conexión a la base de datos

// Manejo del formulario de agregar, editar y eliminar artículos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['articulos'])) { // Adición de artículos
        $articulosData = $_POST['articulos'];
        foreach ($articulosData as $index => $articulo) {
            $nombre = $articulo['nombre'];
            $descripcion = $articulo['descripcion'];
            $precio = $articulo['precio'];
            $stock = $articulo['stock'];

            // Manejo de la imagen
            $imagen = $_FILES['imagen']['name'][$index];
            $imagen_tmp = $_FILES['imagen']['tmp_name'][$index];
            $upload_dir = '../assets/uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            // Mover archivo subido a 'uploads'
            if (move_uploaded_file($imagen_tmp, $upload_dir . $imagen)) {
                $query = "INSERT INTO articulos (nombre, descripcion, precio, stock, imagen) VALUES ('$nombre', '$descripcion', '$precio', '$stock', '$imagen')";
                if ($conn->query($query) === TRUE) {
                    echo "<div class='alert alert-success'>Artículo '$nombre' agregado con éxito.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error al agregar el artículo '$nombre': " . $conn->error . "</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Error al subir la imagen de '$nombre'.</div>";
            }
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['eliminar'])) { // Eliminación de artículos
        $articulo_id = intval($_POST['articulo_id']);
        $query = "DELETE FROM articulos WHERE id = $articulo_id";
        if ($conn->query($query) === TRUE) echo "<div class='alert alert-success'>Artículo eliminado con éxito.</div>";
        else echo "<div class='alert alert-danger'>Error al eliminar el artículo: " . $conn->error . "</div>";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['editar'])) { // Edición de artículos
        $id = intval($_POST['id']);
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];
        $stock = $_POST['stock'];
        $imagen = $_FILES['imagen']['name'];
        $imagen_tmp = $_FILES['imagen']['tmp_name'];

        if (!empty($imagen)) {
            $upload_dir = '../assets/uploads/';
            move_uploaded_file($imagen_tmp, $upload_dir . $imagen);
            $query = "UPDATE articulos SET nombre='$nombre', descripcion='$descripcion', precio='$precio', stock='$stock', imagen='$imagen' WHERE id=$id";
        } else {
            $query = "UPDATE articulos SET nombre='$nombre', descripcion='$descripcion', precio='$precio', stock='$stock' WHERE id=$id";
        }

        if ($conn->query($query) === TRUE) echo "<div class='alert alert-success'>Artículo editado con éxito.</div>";
        else echo "<div class='alert alert-danger'>Error al editar el artículo: " . $conn->error . "</div>";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Consulta para obtener los artículos de la tabla 'articulos'
$query = "SELECT * FROM articulos";
$articulos = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Artículos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-custom { background-color: #0056b3; }
        .navbar-custom .navbar-brand, .navbar-custom .nav-link { color: #fff; }
        .hero-section { background-color: #f8f9fa; text-align: center; padding: 40px 0; position: relative; }
        .hero-section img { width: 100%; max-height: 500px; object-fit: cover; }
        .hero-section .hero-content { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #fff; text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7); }
        .card { transition: transform 0.2s; }
        .card:hover { transform: scale(1.05); }
        .price { color: #dc3545; font-weight: bold; }
        footer { background-color: #343a40; color: white; padding: 20px 0; text-align: center; }
        .form-container { display: none; overflow: hidden; transition: max-height 0.3s ease-in-out; max-height: 0; }
        .form-container.active { display: block; max-height: 500px; }
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
                <li class="nav-item"><a class="nav-link" href="figura.php">Figura</a></li>
                    <li class="nav-item"><button class="nav-link btn" id="toggle-form-button">Agregar</button></li>
                    <li class="nav-item"><a class="nav-link" href="carrito.php">Carrito de compras</a></li>
                    <li class="nav-item"><a class="nav-link" href="graficas.php">Gráficas</a></li> <!-- Nueva opción de Gráficas -->
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
            <h1>Electric Daisy Carnival - Autódromo Hermanos Rodríguez</h1>
            <a href="crud.php" class="btn btn-primary">Ver Boletos</a>
        </div>
    </div>

    <div class="container my-5 form-container" id="form-container">
        <h2>Agregar Artículos</h2>
        <form action="" method="post" enctype="multipart/form-data" id="articulos-form">
            <div id="articulos-container">
                <div class="articulo mb-3">
                    <h5>Artículo 1</h5>
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" name="articulos[0][nombre]" required>
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" name="articulos[0][descripcion]" required></textarea>
                    <label for="precio" class="form-label">Precio</label>
                    <input type="number" class="form-control" name="articulos[0][precio]" step="0.01" required>
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" class="form-control" name="articulos[0][stock]" required>
                    <label for="imagen" class="form-label">Imagen</label>
                    <input type="file" class="form-control" name="imagen[]" accept="image/*" required>
                </div>
            </div>
            <button type="button" class="btn btn-secondary mb-3" id="add-articulo-button">Agregar otro artículo</button>
            <button type="submit" class="btn btn-primary">Agregar Artículos</button>
        </form>
    </div>

    <div class="container my-5">
        <div class="row">
            <?php $articulos->data_seek(0);
            if ($articulos && $articulos->num_rows > 0):
                while ($articulo = $articulos->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <a href="boletos.php?articulo_id=<?php echo $articulo['id']; ?>" class="text-decoration-none">
                            <div class="card h-100">
                                <img src="../assets/uploads/<?php echo $articulo['imagen']; ?>" class="card-img-top" alt="<?php echo $articulo['nombre']; ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $articulo['nombre']; ?></h5>
                                    <p class="card-text"><?php echo $articulo['descripcion']; ?></p>
                                    <p class="price">$<?php echo $articulo['precio']; ?> MXN</p>
                                    <p>Stock: <?php echo $articulo['stock']; ?></p>
                                </div>
                                <div class="card-footer">
                                    <form action="" method="post">
                                        <input type="hidden" name="articulo_id" value="<?php echo $articulo['id']; ?>">
                                        <button type="submit" name="eliminar" class="btn btn-danger w-100">Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endwhile;
            else: ?>
                <p>No hay artículos disponibles.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 TicketsMX - Todos los derechos reservados</p>
    </footer>

    <script>
        document.getElementById('toggle-form-button').addEventListener('click', () => {
            document.getElementById('form-container').classList.toggle('active');
        });

        let contadorArticulos = 1;
        document.getElementById('add-articulo-button').addEventListener('click', () => {
            contadorArticulos++;
            const articuloContainer = document.getElementById('articulos-container');
            const nuevoArticulo = document.createElement('div');
            nuevoArticulo.classList.add('articulo', 'mb-3');
            nuevoArticulo.innerHTML = `<h5>Artículo ${contadorArticulos}</h5>
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" name="articulos[${contadorArticulos - 1}][nombre]" required>
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" name="articulos[${contadorArticulos - 1}][descripcion]" required></textarea>
                <label for="precio" class="form-label">Precio</label>
                <input type="number" class="form-control" name="articulos[${contadorArticulos - 1}][precio]" step="0.01" required>
                <label for="stock" class="form-label">Stock</label>
                <input type="number" class="form-control" name="articulos[${contadorArticulos - 1}][stock]" required>
                <label for="imagen" class="form-label">Imagen</label>
                <input type="file" class="form-control" name="imagen[]" accept="image/*" required>`;
            articuloContainer.appendChild(nuevoArticulo);
        });
    </script>
</body>
</html>
