<?php
require_once '../sql/conexion.php';

$conn = (new Conexion())->conectar();
$action = $_GET['action'] ?? '';

// LISTAR CURSOS (tipo_evento_id debe apuntar al tipo 'curso', asumiremos id=1)
if ($action === 'listar') {
    $stmt = $conn->prepare("
        SELECT e.*, t.nombre AS tipo_nombre, c.nombre AS categoria_nombre
        FROM eventos e
        JOIN tipos_evento t ON e.tipo_evento_id = t.id
        JOIN categorias_evento c ON e.categoria_id = c.id
        WHERE e.tipo_evento_id = 1
    ");
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// OBTENER CURSO POR ID
if ($action === 'obtener' && isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM eventos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    exit;
}

// CREAR CURSO
if ($action === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "INSERT INTO eventos (
        nombre_evento, tipo_evento_id, categoria_id, ponentes,
        fecha_inicio, fecha_fin, horas, cupos, estado
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $success = $stmt->execute([
        $_POST['nombre_evento'],
        $_POST['tipo_evento_id'],
        $_POST['categoria_id'],
        $_POST['ponentes'],
        $_POST['fecha_inicio'],
        $_POST['fecha_fin'],
        $_POST['horas'],
        $_POST['cupos'],
        $_POST['estado']
    ]);
    echo json_encode(['success' => $success]);
    exit;
}

// ACTUALIZAR CURSO
if ($action === 'editar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "UPDATE eventos SET
        nombre_evento=?, tipo_evento_id=?, categoria_id=?, ponentes=?,
        fecha_inicio=?, fecha_fin=?, horas=?, cupos=?, estado=?
        WHERE id=?";
    $stmt = $conn->prepare($sql);
    $success = $stmt->execute([
        $_POST['nombre_evento'],
        $_POST['tipo_evento_id'],
        $_POST['categoria_id'],
        $_POST['ponentes'],
        $_POST['fecha_inicio'],
        $_POST['fecha_fin'],
        $_POST['horas'],
        $_POST['cupos'],
        $_POST['estado'],
        $_POST['id']
    ]);
    echo json_encode(['success' => $success]);
    exit;
}

// ELIMINAR CURSO
if ($action === 'eliminar' && isset($_GET['id'])) {
    $stmt = $conn->prepare("DELETE FROM eventos WHERE id = ?");
    $success = $stmt->execute([$_GET['id']]);
    echo json_encode(['success' => $success]);
    exit;
}

echo json_encode(['error' => 'Acción no válida']);
?>
