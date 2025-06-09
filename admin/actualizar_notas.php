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

foreach ($notas as $inscripcionId => $nota) {
    if ($nota === '') continue; // No actualizar si está vacío
    $stmt = $conexion->prepare("UPDATE inscripciones SET nota = ? WHERE id = ?");
    $stmt->execute([$nota, $inscripcionId]);
}

foreach ($asistencias as $inscripcionId => $asistencia) {
    if ($asistencia === '') continue; // No actualizar si está vacío
    $stmt = $conexion->prepare("UPDATE inscripciones SET asistencia = ? WHERE id = ?");
    $stmt->execute([$asistencia, $inscripcionId]);
}

header("Location: administrar_evento.php?id=" . ($_POST['evento_id'] ?? 0));
exit;
