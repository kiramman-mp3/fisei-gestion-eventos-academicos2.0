<?php
require_once '../session.php';
require '../sql/conexion.php';
require '../libs/fpdf/fpdf.php'; // Asegúrate de que la ruta sea correcta

if (!isLoggedIn()) {
    exit("Acceso no autorizado");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit("ID de evento no válido");
}

$id = (int)$_GET['id'];
$conexion = (new Conexion())->conectar();

// Obtener datos del evento
$stmt = $conexion->prepare("
    SELECT e.*, t.nombre AS tipo_evento, c.nombre AS categoria 
    FROM eventos e
    JOIN tipos_evento t ON e.tipo_evento_id = t.id
    JOIN categorias_evento c ON e.categoria_id = c.id
    WHERE e.id = ?
");
$stmt->execute([$id]);
$evento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$evento) {
    exit("Evento no encontrado");
}

// Obtener inscritos
$insStmt = $conexion->prepare("
    SELECT i.estado, i.nota, i.asistencia,
           e.nombre, e.apellido, e.cedula
    FROM inscripciones i
    JOIN estudiantes e ON i.usuario_id = e.id
    WHERE i.evento_id = ?
");
$insStmt->execute([$id]);
$inscritos = $insStmt->fetchAll(PDO::FETCH_ASSOC);

// ====== FPDF CONFIGURACIÓN ======
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'Reporte del Evento',0,1,'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,utf8_decode('Página ') . $this->PageNo(),0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);

// ====== Datos del Evento ======
$pdf->Cell(0,10,'Nombre: ' . utf8_decode($evento['nombre_evento']),0,1);
$pdf->Cell(0,10,'Tipo: ' . utf8_decode($evento['tipo_evento']),0,1);
$pdf->Cell(0,10,'Categoria: ' . utf8_decode($evento['categoria']),0,1);
$pdf->Cell(0,10,'Fechas: ' . $evento['fecha_inicio'] . ' al ' . $evento['fecha_fin'],0,1);
$pdf->Cell(0,10,'Ponente(s): ' . utf8_decode($evento['ponentes']),0,1);
$pdf->Cell(0,10,'Horas: ' . $evento['horas'] . ' | Cupos: ' . $evento['cupos'],0,1);
$pdf->Ln(5);

// ====== Tabla de Inscritos ======
$pdf->SetFont('Arial','B',11);
$pdf->SetFillColor(182, 27, 40);
$pdf->SetTextColor(255);
$pdf->Cell(10,10,'#',1,0,'C',true);
$pdf->Cell(40,10,'Cédula',1,0,'C',true);
$pdf->Cell(60,10,'Nombre',1,0,'C',true);
$pdf->Cell(25,10,'Nota',1,0,'C',true);
$pdf->Cell(25,10,'Asist.',1,0,'C',true);
$pdf->Cell(30,10,'Estado',1,1,'C',true);

$pdf->SetFont('Arial','',10);
$pdf->SetTextColor(0);

foreach ($inscritos as $i => $row) {
    $pdf->Cell(10,10,$i+1,1);
    $pdf->Cell(40,10,$row['cedula'],1);
    $pdf->Cell(60,10,utf8_decode($row['nombre'] . ' ' . $row['apellido']),1);
    $pdf->Cell(25,10,is_null($row['nota']) ? '-' : $row['nota'],1);
    $pdf->Cell(25,10,is_null($row['asistencia']) ? '-' : $row['asistencia'].'%',1);
    $pdf->Cell(30,10,utf8_decode($row['estado']),1,1);
}

$pdf->Output('I', 'evento_' . $evento['id'] . '.pdf');
exit;
