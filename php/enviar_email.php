<?php
function enviar_email_con_adjunto($correo_destino, $archivo_pdf) {
    $to = $correo_destino;
    $subject = "Confirmación de Compra de Boletos";
    $message = "Gracias por tu compra. Adjuntamos el boleto en formato PDF.";
    $headers = "From: noreply@ticketsmx.com";

    // Lógica para adjuntar el PDF en el correo
    $separator = md5(time());
    $eol = PHP_EOL;
    $pdf_content = file_get_contents($archivo_pdf);
    $pdf_attachment = chunk_split(base64_encode($pdf_content));

    // Encabezados para adjuntar el PDF
    $headers .= "MIME-Version: 1.0" . $eol;
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;

    // Mensaje del correo
    $body = "--" . $separator . $eol;
    $body .= "Content-Type: text/plain; charset=\"iso-8859-1\"" . $eol;
    $body .= "Content-Transfer-Encoding: 7bit" . $eol;
    $body .= $message . $eol;

    // Adjuntar el PDF
    $body .= "--" . $separator . $eol;
    $body .= "Content-Type: application/octet-stream; name=\"" . basename($archivo_pdf) . "\"" . $eol;
    $body .= "Content-Transfer-Encoding: base64" . $eol;
    $body .= "Content-Disposition: attachment" . $eol . $eol;
    $body .= $pdf_attachment . $eol;
    $body .= "--" . $separator . "--";

    if (mail($to, $subject, "", $headers . $body)) {
        echo "Correo enviado a $to.";
    } else {
        echo "Error al enviar el correo.";
    }
}
?>
