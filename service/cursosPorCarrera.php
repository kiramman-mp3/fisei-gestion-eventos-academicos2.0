<?php
// controller/CursosPorCarrera.php
require_once '../session.php';
require_once '../sql/conexion.php'; // o como se llame tu conexión

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$rol = getUserRole();
$carrera= getUserCarrera();

$sql = "
    SELECT e.id, e.nombre_evento, e.fecha_inicio, e.fecha_fin, e.horas, e.cupos, e.ruta_imagen, e.ponentes
    FROM eventos e
    INNER JOIN categorias_evento c ON e.categoria_id = c.id
    WHERE c.nombre = ?
";

$cris = new Conexion();
$conn = $cris->conectar();
$stmt = $conn->prepare($sql);
$stmt->execute([$carrera]);
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'rol' => $rol,
    'cursos' => $cursos
]);
