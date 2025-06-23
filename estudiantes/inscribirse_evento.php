<?php
require_once '../session.php';
require_once '../sql/conexion.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión.']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$evento_id = $data['evento_id'] ?? null;
$usuario_id = getUserId();

if (!$evento_id || !$usuario_id) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit;
}

try {
    $conn = (new Conexion())->conectar();

    // 1. Validar si ya está inscrito
    $stmt = $conn->prepare("SELECT COUNT(*) FROM inscripciones WHERE usuario_id = ? AND evento_id = ?");
    $stmt->execute([$usuario_id, $evento_id]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Ya estás inscrito en este curso.']);
        exit;
    }

    // 2. Insertar inscripción
    $stmt = $conn->prepare("INSERT INTO inscripciones (usuario_id, evento_id) VALUES (?, ?)");
    $stmt->execute([$usuario_id, $evento_id]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al inscribirse: ' . $e->getMessage()]);
}
