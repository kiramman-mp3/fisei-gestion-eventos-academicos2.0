<?php
// ====== cursosporcarrera.php ======
require_once '../session.php';
require_once '../sql/conexion.php';

header('Content-Type: application/json');

$conn = (new Conexion())->conectar();

try {
    $rol = isLoggedIn() ? getUserRole() : null;
    $carrera = isLoggedIn() ? getUserCarrera() : null;
    $usuario_id = isLoggedIn() ? getUserId() : null;

    $sqlBase = "
        SELECT e.*, c.nombre AS categoria_nombre, t.nombre AS tipo_nombre
        FROM eventos e
        JOIN categorias_evento c ON e.categoria_id = c.id
        JOIN tipos_evento t ON e.tipo_evento_id = t.id
        WHERE e.estado = 'abierto'
    ";

    if ($rol === 'estudiante' && !empty($carrera)) {
        $sqlBase .= " AND c.nombre = ?";
        $stmt = $conn->prepare($sqlBase);
        $stmt->execute([$carrera]);
    } else {
        $stmt = $conn->prepare($sqlBase);
        $stmt->execute();
    }

    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener inscripciones del estudiante si aplica
    $idsInscritos = [];
    if ($rol === 'estudiante' && $usuario_id) {
        $stmt2 = $conn->prepare("SELECT evento_id FROM inscripciones WHERE usuario_id = ?");
        $stmt2->execute([$usuario_id]);
        $idsInscritos = $stmt2->fetchAll(PDO::FETCH_COLUMN);
    }

    foreach ($cursos as &$curso) {
        $curso['inscrito'] = in_array($curso['id'], $idsInscritos);
    }

    echo json_encode([
        'rol' => $rol,
        'usuario_id' => $usuario_id,
        'cursos' => $cursos
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'rol' => null,
        'usuario_id' => null,
        'cursos' => [],
        'error' => $e->getMessage()
    ]);
}