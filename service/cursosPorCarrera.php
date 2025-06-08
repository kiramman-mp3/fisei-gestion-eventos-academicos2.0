<?php
// controller/CursosPorCarrera.php
require_once '../includes/session.php';
require_once '../sql/conexion.php'; // o como se llame tu conexión

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$carrera = getUserCarrera();  // por nombre literal
$rol = getUserRole();

// Asegúrate que en la BD haya una relación entre cursos y carrera por nombre (o una tabla intermedia)
$sql = "
    SELECT c.id, c.nombre_evento, c.fecha_inicio, c.fecha_fin
    FROM cursos c
    INNER JOIN carreras_evento ce ON c.carrera_id = ce.id
    WHERE ce.nombre = ?
";

$stmt = $conn->prepare($sql);
$stmt->execute([$carrera]);
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'rol' => $rol,
    'cursos' => $cursos
]);
