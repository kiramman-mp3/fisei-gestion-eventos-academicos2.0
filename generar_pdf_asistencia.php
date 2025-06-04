<?php
require('fpdf/fpdf.php');

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);

// Título
$pdf->Cell(0, 10, 'Reporte de Asistencia - Universidad Técnica de Ambato', 0, 1, 'C');
$pdf->Ln(10);

// Encabezados de la tabla
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(50, 10, 'Nombre', 1);
$pdf->Cell(50, 10, 'Curso', 1);
$pdf->Cell(40, 10, 'Fecha', 1);
$pdf->Cell(40, 10, 'Asistencia', 1);
$pdf->Ln();

// Datos (puedes reemplazar con datos reales desde una BD si deseas)
$datos = [
    ['Andrea López', 'Python Básico', '2025-06-03', 'Presente'],
    ['Carlos Pérez', 'Python Básico', '2025-06-03', 'Ausente'],
    ['Valeria Ruiz', 'Java Avanzado', '2025-06-03', 'Presente'],
];

$pdf->SetFont('Arial', '', 12);
foreach ($datos as $fila) {
    $pdf->Cell(50, 10, $fila[0], 1);
    $pdf->Cell(50, 10, $fila[1], 1);
    $pdf->Cell(40, 10, $fila[2], 1);
    $pdf->Cell(40, 10, $fila[3], 1);
    $pdf->Ln();
}

$pdf->Output('D', 'reporte_asistencia.pdf'); // 'D' fuerza la descarga
?>
