<?php
require 'dompdf/autoload.inc.php'; // Asegúrate de tener dompdf correctamente instalado
use Dompdf\Dompdf;

function guardar_pdf($html_content, $filename, $guardar_en_servidor = false) {
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html_content);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    if ($guardar_en_servidor) {
        $output = $dompdf->output();
        $pdf_file_path = __DIR__ . '/' . $filename;
        file_put_contents($pdf_file_path, $output);
        return $pdf_file_path; // Retorna la ruta del archivo guardado
    } else {
        $dompdf->stream($filename); // Descarga el archivo PDF
    }
}
?>
