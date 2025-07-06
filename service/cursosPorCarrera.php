<?php
require_once '../session.php';
require_once '../sql/conexion.php';

header('Content-Type: application/json');

$conn = (new Conexion())->conectar();

try {
    $rol = isLoggedIn() ? getUserRole() : null;
    $carrera = isLoggedIn() ? getUserCarrera() : null;
    $usuario_id = isLoggedIn() ? getUserId() : null;

    // Base con JOIN a evento_categoria para obtener categorías múltiples (concatenadas)
    $sqlBase = "
        SELECT e.*, 
               GROUP_CONCAT(DISTINCT c.nombre ORDER BY c.nombre SEPARATOR ', ') AS categoria_nombres,
               t.nombre AS tipo_nombre,
               COUNT(DISTINCT i.id) as inscritos_actuales,
               (e.cupos - COUNT(DISTINCT i.id)) as cupos_disponibles
        FROM eventos e
        JOIN evento_categoria ec ON ec.evento_id = e.id
        JOIN categorias_evento c ON c.id = ec.categoria_id
        JOIN tipos_evento t ON e.tipo_evento_id = t.id
        LEFT JOIN inscripciones i ON e.id = i.evento_id
        WHERE e.estado = 'abierto'
    ";

    // Parámetros para preparar la consulta
    $params = [];

    if ($rol === 'estudiante') {
        if (!empty($carrera)) {
            // Filtrar eventos que tengan al menos una categoría igual a la carrera del estudiante
            $sqlBase .= " AND c.nombre = ?";
            $params[] = $carrera;
        } else {
            // Si no tiene carrera, filtra por categoría con id = 2 (por ejemplo)
            $sqlBase .= " AND c.id = 2";
        }
    }

    $sqlBase .= " GROUP BY e.id, t.nombre";

    $stmt = $conn->prepare($sqlBase);
    $stmt->execute($params);

    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener eventos a los que está inscrito el usuario
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
