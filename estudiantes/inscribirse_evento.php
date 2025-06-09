<?php
require_once '../session.php';
include('../sql/conexion.php');

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'No has iniciado sesión.']);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$eventoId = $input['evento_id'] ?? null;
$usuarioId = getUserId();

if (!$eventoId || !is_numeric($eventoId)) {
    echo json_encode(['success' => false, 'message' => 'ID de evento inválido.']);
    exit;
}

$conexion = (new Conexion())->conectar();

// Validar si ya está inscrito
$checkStmt = $conexion->prepare("SELECT COUNT(*) FROM inscripciones WHERE usuario_id = ? AND evento_id = ?");
$checkStmt->execute([$usuarioId, $eventoId]);
if ($checkStmt->fetchColumn() > 0) {
    echo json_encode(['success' => false, 'message' => 'Ya estás inscrito en este evento.']);
    exit;
}

// Insertar inscripción
$insertStmt = $conexion->prepare("
    INSERT INTO inscripciones (usuario_id, evento_id)
    VALUES (?, ?)
");

try {
    $insertStmt->execute([$usuarioId, $eventoId]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en base de datos: ' . $e->getMessage()]);
}
