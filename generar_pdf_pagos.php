<?php
require('fpdf/fpdf.php');

// Simulación de datos
$pagos = [
    ["nombre" => "Juan Pérez", "curso" => "PHP Básico", "monto" => 100.00, "fecha" => "2025-05-10"],
    ["nombre" => "Ana Torres", "curso" => "Java Intermedio", "monto" => 120.00, "fecha" => "2025-05-12"],
    ["nombre" => "Luis Gómez", "curso" => "Python Avanzado", "monto" => 150.00, "fecha" => "2025-05-14"],
    ["nombre" => "María Rodríguez", "curso" => "HTML y CSS", "monto" => 90.00, "fecha" => "2025-05-15"],
];

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Reporte de Pagos',0,1,'C');
$pdf->Ln(10);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(50,10,'Nombre',1);
$pdf->Cell(60,10,'Curso',1);
$pdf->Cell(30,10,'Monto',1);
$pdf->Cell(40,10,'Fecha',1);
$pdf->Ln();

$pdf->SetFont('Arial','',12);
foreach($pagos as $p){
    $pdf->Cell(50,10,utf8_decode($p['nombre']),1);
    $pdf->Cell(60,10,utf8_decode($p['curso']),1);
    $pdf->Cell(30,10,'$'.number_format($p['monto'], 2),1);
    $pdf->Cell(40,10,$p['fecha'],1);
    $pdf->Ln();
}

$pdf->Output('D', 'reporte_pagos.pdf');
?>
