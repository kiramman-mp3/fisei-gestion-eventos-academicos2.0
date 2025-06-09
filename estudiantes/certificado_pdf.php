<?php
require_once '../session.php';
require '../sql/conexion.php';
require '../libs/fpdf/fpdf.php';

if (!isLoggedIn()) {
    exit("Acceso denegado.");
}

$usuarioId = getUserId();
$eventoId = $_GET['evento_id'] ?? null;

if (!is_numeric($eventoId)) {
    exit("ID inválido");
}

$conexion = (new Conexion())->conectar();

// Validar inscripción
$stmt = $conexion->prepare("
    SELECT e.nombre_evento, e.fecha_inicio, e.fecha_fin, e.ponentes, e.horas,
           i.nota, i.asistencia, i.estado,
           est.nombre, est.apellido, est.cedula
    FROM inscripciones i
    JOIN eventos e ON i.evento_id = e.id
    JOIN estudiantes est ON i.usuario_id = est.id
    WHERE i.usuario_id = ? AND e.id = ?
");
$stmt->execute([$usuarioId, $eventoId]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    exit("No inscrito en este evento.");
}

if ($data['estado'] !== 'Pagado' || $data['nota'] < 7 || $data['asistencia'] < 70) {
    exit("No cumple los requisitos para obtener el certificado.");
}

// Generar PDF
class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Certificado de Participación', 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 9);
        $this->Cell(0, 10, utf8_decode('FISEI - Universidad Técnica de Ambato'), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

$nombreCompleto = $data['nombre'] . ' ' . $data['apellido'];
$nombreCurso = $data['nombre_evento'];
$fechas = $data['fecha_inicio'] . ' al ' . $data['fecha_fin'];
$horas = $data['horas'];

$pdf->Ln(10);
$pdf->MultiCell(0, 10, utf8_decode("Se certifica que el(la) estudiante $nombreCompleto con cédula {$data['cedula']} ha participado satisfactoriamente en el curso \"$nombreCurso\", realizado del $fechas con una duración total de $horas horas académicas."), 0, 'J');

$pdf->Ln(20);
$pdf->Cell(0, 10, '_________________________', 0, 1, 'C');
$pdf->Cell(0, 6, 'Firma del Coordinador', 0, 1, 'C');

$pdf->Output('I', 'certificado_' . $eventoId . '.pdf');
exit;
