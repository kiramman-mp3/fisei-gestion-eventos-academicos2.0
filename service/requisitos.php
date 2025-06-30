<?php
require_once '../session.php';
require_once '../sql/conexion.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

$evento_id = $_GET['evento_id'] ?? null;
$usuario_id = getUserId();

if (!$evento_id || !$usuario_id) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

try {
    $conn = (new Conexion())->conectar();

    // Obtener requisitos
    $stmt = $conn->prepare("SELECT id, descripcion FROM requisitos_evento WHERE evento_id = ?");
    $stmt->execute([$evento_id]);
    $requisitos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener inscripción
    $stmt2 = $conn->prepare("SELECT id FROM inscripciones WHERE evento_id = ? AND usuario_id = ?");
    $stmt2->execute([$evento_id, $usuario_id]);
    $inscripcion = $stmt2->fetch(PDO::FETCH_ASSOC);
    $inscripcion_id = $inscripcion['id'] ?? null;

    $entregados = [];
    if ($inscripcion_id) {
        $stmt3 = $conn->prepare("SELECT requisito_id FROM requisitos_inscripcion WHERE inscripcion_id = ?");
        $stmt3->execute([$inscripcion_id]);
        $entregados = array_column($stmt3->fetchAll(PDO::FETCH_ASSOC), 'requisito_id');
    }

    foreach ($requisitos as &$req) {
        $req['cumplido'] = in_array($req['id'], $entregados);
    }

    // Traer también info del curso
    $stmt4 = $conn->prepare("SELECT * FROM eventos WHERE id = ?");
    $stmt4->execute([$evento_id]);
    $curso = $stmt4->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'requisitos' => $requisitos,
        'curso' => $curso
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en BD: ' . $e->getMessage()]);
}
