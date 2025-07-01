<?php
require_once '../session.php';
include('../sql/conexion.php');

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$conexion = (new Conexion())->conectar();

// Obtener el evento_id desde el POST
$evento_id = $_POST['evento_id'] ?? null;
if (!$evento_id) {
    echo "Error: ID del evento no proporcionado.";
    exit;
}

// Obtener los requisitos de la categoría del evento
$stmt = $conexion->prepare("
    SELECT c.requiere_nota, c.requiere_asistencia, c.nombre as categoria_nombre
    FROM eventos e
    JOIN categorias_evento c ON e.categoria_id = c.id
    WHERE e.id = ?
");
$stmt->execute([$evento_id]);
$categoria_requisitos = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$categoria_requisitos) {
    echo "Error: No se pudo obtener información de la categoría.";
    exit;
}

$notas = $_POST['notas'] ?? [];
$asistencias = $_POST['asistencias'] ?? [];
$legalizados = $_POST['legalizados'] ?? [];
$pagos = $_POST['pagos'] ?? [];

// Validar campos obligatorios según la categoría
$errores = [];

foreach ($notas as $inscripcionId => $nota) {
    if ($categoria_requisitos['requiere_nota'] && ($nota === '' || $nota === null)) {
        $errores[] = "La nota es obligatoria para la categoría '{$categoria_requisitos['categoria_nombre']}'.";
    }
    if ($nota !== '' && $nota !== null) {
        $stmt = $conexion->prepare("UPDATE inscripciones SET nota = ? WHERE id = ?");
        $stmt->execute([$nota, $inscripcionId]);
    }
}

foreach ($asistencias as $inscripcionId => $asistencia) {
    if ($categoria_requisitos['requiere_asistencia'] && ($asistencia === '' || $asistencia === null)) {
        $errores[] = "La asistencia es obligatoria para la categoría '{$categoria_requisitos['categoria_nombre']}'.";
    }
    if ($asistencia !== '' && $asistencia !== null) {
        $stmt = $conexion->prepare("UPDATE inscripciones SET asistencia = ? WHERE id = ?");
        $stmt->execute([$asistencia, $inscripcionId]);
    }
}

// Si hay errores, mostrarlos y no continuar
if (!empty($errores)) {
    echo "<h3>Errores de validación:</h3>";
    echo "<ul>";
    foreach ($errores as $error) {
        echo "<li style='color: red;'>$error</li>";
    }
    echo "</ul>";
    echo "<br><a href='administrar_evento.php?id=$evento_id' class='btn btn-secondary'>Volver</a>";
    exit;
}

// Marcar como legalizado (si está presente en el POST)
$stmtLegalizado = $conexion->prepare("UPDATE inscripciones SET legalizado = ? WHERE id = ?");
foreach ($notas as $inscripcionId => $_) {
    $valor = isset($legalizados[$inscripcionId]) ? 1 : 0;
    $stmtLegalizado->execute([$valor, $inscripcionId]);
}

// Marcar como pago confirmado (si está presente en el POST)
$stmtPago = $conexion->prepare("UPDATE inscripciones SET pago_confirmado = ? WHERE id = ?");
foreach ($notas as $inscripcionId => $_) {
    $valor = isset($pagos[$inscripcionId]) ? 1 : 0;
    $stmtPago->execute([$valor, $inscripcionId]);
}

$eventoId = $_POST['evento_id'] ?? 0;
header("Location: administrar_evento.php?id=$eventoId");
exit;
