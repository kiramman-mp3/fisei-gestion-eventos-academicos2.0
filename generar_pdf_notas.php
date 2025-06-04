<?php
require('fpdf/fpdf.php');

// Simulación de datos
$notas = [
    ["nombre" => "Juan Pérez", "curso" => "PHP Básico", "nota" => 18],
    ["nombre" => "Ana Torres", "curso" => "Java Intermedio", "nota" => 16],
    ["nombre" => "Luis Gómez", "curso" => "Python Avanzado", "nota" => 20],
    ["nombre" => "María Rodríguez", "curso" => "HTML y CSS", "nota" => 17],
];

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Reporte de Notas',0,1,'C');
$pdf->Ln(10);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(60,10,'Nombre',1);
$pdf->Cell(70,10,'Curso',1);
$pdf->Cell(30,10,'Nota',1);
$pdf->Ln();

$pdf->SetFont('Arial','',12);
foreach($notas as $n){
    $pdf->Cell(60,10,utf8_decode($n['nombre']),1);
    $pdf->Cell(70,10,utf8_decode($n['curso']),1);
    $pdf->Cell(30,10,$n['nota'],1);
    $pdf->Ln();
}

$pdf->Output('D', 'reporte_notas.pdf');
?>
