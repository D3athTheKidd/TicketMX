<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Mexico_City');

// Incluir los archivos de PHPMailer, FPDF y TCPDF
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../tcpdf/tcpdf.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Verificar si se ha confirmado el pago
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirmar_pago'])) {
    $fecha_compra = date('d-m-Y');
    $hora_compra = date('H:i:s');

    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Tickets MX');
    $pdf->SetTitle('Boletos Comprados');
    $pdf->SetSubject('Detalles de compra');

    // Verificar que haya elementos en el carrito
    if (!empty($_SESSION['carrito'])) {
        foreach ($_SESSION['carrito'] as $item) {
            $pdf->AddPage();

            $imagePath = __DIR__ . '/../assets/uploads/' . $item['imagen'];

            if (file_exists($imagePath)) {
                $pdf->Image($imagePath, 40, 20, 120, 60, 'JPG');
            } else {
                $pdf->Cell(0, 20, 'Imagen no disponible', 0, 1, 'C');
            }

            $pdf->Ln(70);

            $html = '<h2 style="text-align: center;">' . $item['nombre'] . '</h2>';
            $html .= '<p style="text-align: center;">Fecha del evento: 30 de Marzo, 2023<br>CIUDAD DE MÉXICO - PEPSI CENTER WTC</p>';
            $html .= '<p style="text-align: center; font-size: 12px; margin-top: 5px;">Precio: $' . number_format($item['precio'], 2) . '</p>';
            $html .= '<p style="text-align: center; font-size: 12px; margin-top: -10px;">Cantidad: ' . $item['cantidad'] . '</p>';
            $html .= '<p style="text-align: center; font-size: 10px; margin-top: 10px;">Fecha de compra: ' . $fecha_compra . ' - ' . $hora_compra . '</p>';

            $pdf->writeHTML($html, true, false, true, false, '');

            $style = array(
                'border' => 1,
                'padding' => 4,
                'fgcolor' => array(0, 0, 0),
                'bgcolor' => false
            );
            $pdf->write2DBarcode($item['nombre'], 'QRCODE,H', 15, 120, 45, 45, $style, 'N');
        }

        ob_end_clean(); // Limpiar el buffer de salida antes de generar el PDF
        $pdfFile = __DIR__ . '/../boletos_comprados.pdf';
        $pdf->Output($pdfFile, 'F');

        if (file_exists($pdfFile)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="boletos_comprados.pdf"');
            header('Content-Length: ' . filesize($pdfFile));
            readfile($pdfFile);
            exit;
        } else {
            header("Location:checkout.php?mensaje=El archivo PDF no existe.");
            exit;
        }
    } else {
        header("Location:checkout.php?mensaje=El carrito está vacío.");
        exit;
    }
}

// Verificar si se envió el correo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['enviar_correo'])) {
    $fecha_compra = date('d-m-Y');
    $hora_compra = date('H:i:s');

    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Tickets MX');
    $pdf->SetTitle('Boletos Comprados');
    $pdf->SetSubject('Detalles de compra');

    if (!empty($_SESSION['carrito'])) {
        foreach ($_SESSION['carrito'] as $item) {
            $pdf->AddPage();

            $imagePath = __DIR__ . '/../assets/uploads/' . $item['imagen'];

            if (file_exists($imagePath)) {
                $pdf->Image($imagePath, 40, 20, 120, 60, 'JPG');
            } else {
                $pdf->Cell(0, 20, 'Imagen no disponible', 0, 1, 'C');
            }

            $pdf->Ln(70);

            $html = '<h2 style="text-align: center;">' . $item['nombre'] . '</h2>';
            $html .= '<p style="text-align: center;">Fecha del evento: 30 de Marzo, 2023<br>CIUDAD DE MÉXICO - PEPSI CENTER WTC</p>';
            $html .= '<p style="text-align: center; font-size: 12px; margin-top: 5px;">Precio: $' . number_format($item['precio'], 2) . '</p>';
            $html .= '<p style="text-align: center; font-size: 12px; margin-top: -10px;">Cantidad: ' . $item['cantidad'] . '</p>';
            $html .= '<p style="text-align: center; font-size: 10px; margin-top: 10px;">Fecha de compra: ' . $fecha_compra . ' - ' . $hora_compra . '</p>';

            $pdf->writeHTML($html, true, false, true, false, '');

            $style = array(
                'border' => 1,
                'padding' => 4,
                'fgcolor' => array(0, 0, 0),
                'bgcolor' => false
            );
            $pdf->write2DBarcode($item['nombre'], 'QRCODE,H', 15, 120, 45, 45, $style, 'N');
        }

        ob_end_clean();
        $pdfFile = __DIR__ . '/../boletos_comprados.pdf';
        $pdf->Output($pdfFile, 'F');

        if (file_exists($pdfFile)) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'd3aththekid2077@gmail.com';
                $mail->Password = 'orls dmez wmum mjhg';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('TicketMX@gmail.com', 'Compra de Producto');
                $mail->addAddress($_POST['correo']);
                $mail->isHTML(true);
                $mail->Subject = 'Comprobante de compra de boletos';
                $mail->Body = 'Adjunto encontrarás el comprobante de tus boletos. ¡Gracias por tu compra!';

                $mail->addAttachment($pdfFile);
                $mail->send();

                unlink($pdfFile);
                header("Location:checkout.php?mensaje=El comprobante ha sido enviado a tu correo.");
                exit;
            } catch (Exception $e) {
                header("Location:checkout.php?mensaje=Error al enviar el correo: " . $mail->ErrorInfo);
                exit;
            }
        } else {
            header("Location:checkout.php?mensaje=El archivo PDF no existe.");
            exit;
        }
    } else {
        header("Location:checkout.php?mensaje=El carrito está vacío.");
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e9f3fc; /* Fondo azul claro */
        }
        h1 {
            color: #007bff; /* Título azul */
            text-align: center;
            margin-bottom: 20px;
        }
        .alert {
            background-color: #cce5ff; /* Color de alerta azul claro */
            border-color: #b8daff; /* Borde de alerta azul */
            color: #004085; /* Color del texto de la alerta */
        }
        .card {
            border: 1px solid #007bff; /* Borde azul para las tarjetas */
        }
        .btn-primary {
            background-color: #007bff; /* Botón azul */
            border-color: #0056b3; /* Borde del botón azul más oscuro */
        }
        .btn-secondary {
            background-color: #6c757d; /* Botón secundario gris */
            border-color: #5a6268; /* Borde del botón secundario */
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h1>Detalles de tu compra</h1>

        <?php if (!empty($_SESSION['mensaje'])): ?>
            <div class="alert alert-info">
                <?php echo $_SESSION['mensaje']; ?>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['carrito'])): ?>
            <div class="row">
                <?php foreach ($_SESSION['carrito'] as $item): ?>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img src="../assets/uploads/<?php echo $item['imagen']; ?>" class="card-img-top" alt="Imagen del Boleto">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $item['nombre']; ?></h5>
                                <p class="card-text">Precio: $<?php echo number_format($item['precio'], 2); ?></p>
                                <p class="card-text">Cantidad: <?php echo $item['cantidad']; ?></p>
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($item['nombre']); ?>" alt="QR Code">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <button type="submit" name="confirmar_pago" class="btn btn-primary">Descargar PDF</button>
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('correoModal').style.display='block'">Enviar por correo</button>
        </form>

    
        <a href="crud.php" class="btn btn-secondary mt-3">Regresar</a>
    </div>

    <div id="correoModal" class="modal">
        <div class="modal-content">
            <span onclick="document.getElementById('correoModal').style.display='none'" class="close" title="Cerrar Modal">&times;</span>
            <form method="post">
                <div class="mb-3">
                    <label for="correo" class="form-label">Ingresa tu correo electrónico:</label>
                    <input type="email" name="correo" class="form-control" required>
                </div>
                <button type="submit" name="enviar_correo" class="btn btn-primary">Enviar PDF por correo</button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('correoModal').style.display='none'">Cancelar</button>
            </form>
        </div>
    </div>
    <script>
        // Modal script
        window.onclick = function(event) {
            if (event.target == document.getElementById('correoModal')) {
                document.getElementById('correoModal').style.display = "none";
            }
        }
    </script>
</body>
</html>