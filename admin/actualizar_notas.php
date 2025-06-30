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

$notas = $_POST['notas'] ?? [];
$asistencias = $_POST['asistencias'] ?? [];
$legalizados = $_POST['legalizados'] ?? [];
$pagos = $_POST['pagos'] ?? [];

foreach ($notas as $inscripcionId => $nota) {
    if ($nota === '') continue;
    $stmt = $conexion->prepare("UPDATE inscripciones SET nota = ? WHERE id = ?");
    $stmt->execute([$nota, $inscripcionId]);
}

foreach ($asistencias as $inscripcionId => $asistencia) {
    if ($asistencia === '') continue;
    $stmt = $conexion->prepare("UPDATE inscripciones SET asistencia = ? WHERE id = ?");
    $stmt->execute([$asistencia, $inscripcionId]);
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
