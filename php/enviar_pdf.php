<?php
session_start();
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Verificar si se ha recibido la solicitud para enviar el PDF
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['correo'])) {
    $pdfFile = __DIR__ . '/../boletos_comprados.pdf';

    // Proceder con el envío de correo solo si se creó el PDF
    if (file_exists($pdfFile)) {
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'd3aththekid2077@gmail.com'; // Reemplaza con tu correo
            $mail->Password = 'orls dmez wmum mjhg'; // Reemplaza con tu contraseña
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Configuración del correo
            $mail->setFrom('d3aththekid2077@gmail.com', 'Compra de Producto');
            $mail->addAddress($_POST['correo']); // Capturar el correo del formulario
            $mail->isHTML(true);
            $mail->Subject = 'Comprobante de compra de boletos';
            $mail->Body = 'Adjunto encontrarás el comprobante de tus boletos. ¡Gracias por tu compra!';

            // Adjuntar el PDF generado
            $mail->addAttachment($pdfFile);
            $mail->send();

            // Eliminar el archivo después de enviarlo
            unlink($pdfFile);
            $_SESSION['mensaje'] = "El comprobante ha sido enviado a tu correo.";
        } catch (Exception $e) {
            $_SESSION['mensaje'] = "Error al enviar el correo: " . $mail->ErrorInfo;
        }
    } else {
        $_SESSION['mensaje'] = "El archivo PDF no existe.";
    }

    // Redirigir de regreso a la página de checkout
    header("Location: checkout.php");
    exit();
}
?>
