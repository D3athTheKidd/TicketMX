<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir TCPDF
require '../tcpdf/tcpdf.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirmar_pago'])) {
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Tickets MX');
    $pdf->SetTitle('Boletos Comprados');
    $pdf->SetSubject('Detalles de compra');

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

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Ln(5);

        $style = array('border' => 1, 'padding' => 4, 'fgcolor' => array(0,0,0), 'bgcolor' => false);
        $pdf->write2DBarcode($item['nombre'], 'QRCODE,H', 15, 120, 45, 45, $style, 'N');
    }

    $pdfFile = __DIR__ . '/../boletos_comprados.pdf';
    $pdf->Output($pdfFile, 'F'); // Guardar el archivo en el sistema

    header("Location: checkout.php?mensaje=PDF generado exitosamente.");
    exit();
}
?>
