<?php
include('db.php'); 

// Consulta para obtener el stock de cada artículo para la gráfica
$stock_query = "SELECT nombre, stock FROM articulos";
$stock_result = $conn->query($stock_query);

$articulos = [];
$stocks = [];

while ($row = $stock_result->fetch_assoc()) {
    $articulos[] = $row['nombre'];
    $stocks[] = $row['stock'];
}

// Obtener el rango de fechas proporcionado por el usuario
$fecha_inicio = $_POST['fecha_inicio'] ?? '';
$fecha_fin = $_POST['fecha_fin'] ?? '';

$ventas = [];
$total_ventas = 0;

if ($fecha_inicio && $fecha_fin) {
    // Consulta para obtener las ventas de artículos en el rango de fechas
    $ventas_query = "SELECT a.nombre, SUM(v.cantidad) AS total_vendido, a.stock, SUM(v.cantidad * a.precio) AS total_ventas 
                     FROM articulos a
                     JOIN ventas v ON a.id = v.articulo_id
                     WHERE v.fecha BETWEEN ? AND ?
                     GROUP BY a.id";
    $stmt = $conn->prepare($ventas_query);
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $ventas_result = $stmt->get_result();

    while ($row = $ventas_result->fetch_assoc()) {
        $ventas[] = [
            'nombre' => $row['nombre'],
            'stock' => $row['stock'],
            'total_vendido' => $row['total_vendido'],
            'total_ventas' => $row['total_ventas']
        ];
        $total_ventas += $row['total_ventas'];
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficas de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e6f2ff;
        }
        .navbar-custom {
            background-color: #004080;
        }
        .navbar-custom .navbar-brand, .navbar-custom .nav-link {
            color: #ffffff;
        }
        .navbar-custom .nav-link:hover {
            color: #cce7ff;
        }
        .container {
            margin-top: 20px;
        }
        h2, h3 {
            color: #004080;
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
                    <li class="nav-item"><a class="nav-link" href="carrito.php">Carrito de compras</a></li>
                    <li class="nav-item"><a class="nav-link" href="graficas.php">Gráficas</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Consultar Ventas por Rango de Fechas</h2>
        <form method="POST" action="">
            <div class="row mb-3">
                <div class="col-md-5">
                    <label for="fecha_inicio" class="form-label">Fecha Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" required>
                </div>
                <div class="col-md-5">
                    <label for="fecha_fin" class="form-label">Fecha Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Consultar Ventas</button>
                </div>
            </div>
        </form>

        <?php if (!empty($ventas)): ?>
            <h3>Ventas en el Rango de Fechas</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Artículo</th>
                        <th>Stock</th>
                        <th>Total Vendido</th>
                        <th>Total en Ventas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ventas as $venta): ?>
                        <tr>
                            <td><?php echo $venta['nombre']; ?></td>
                            <td><?php echo $venta['stock']; ?></td>
                            <td><?php echo $venta['total_vendido']; ?></td>
                            <td><?php echo number_format($venta['total_ventas'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Total de Ventas: <?php echo number_format($total_ventas, 2); ?></h3>
        <?php else: ?>
            <p>No hay ventas en el rango de fechas seleccionado.</p>
        <?php endif; ?>

        <h2>Gráfica de Stock de Artículos</h2>
        <canvas id="stockChart" width="400" height="200"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = <?php echo json_encode($articulos); ?>;
        const data = <?php echo json_encode($stocks); ?>;
        const ctx = document.getElementById('stockChart').getContext('2d');
        const stockChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels, 
                datasets: [{
                    label: 'Stock',
                    data: data, 
                    backgroundColor: 'rgba(0, 64, 128, 0.6)',
                    borderColor: 'rgba(0, 64, 128, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>
