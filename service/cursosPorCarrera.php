<?php
require_once '../session.php';
require_once '../sql/conexion.php';

header('Content-Type: application/json');

$cris = new Conexion();
$conn = $cris->conectar();

if (isLoggedIn()) {
    $rol = getUserRole();
    $carrera = getUserCarrera();

    // Si es docente o administrador, mostrar todos los cursos abiertos
    if ($rol === 'docente' || $rol === 'administrador') {
        $sql = "SELECT * FROM eventos WHERE estado = 'abierto'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    }
    // Si es estudiante con carrera definida, filtrar por carrera
    elseif ($rol === 'estudiante' && !empty($carrera)) {
        $sql = "
            SELECT e.*
            FROM eventos e
            INNER JOIN categorias_evento c ON e.categoria_id = c.id
            WHERE c.nombre = ? AND e.estado = 'abierto'
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$carrera]);
    }
    // Si no hay carrera o el rol es inválido, mostrar todos los cursos
    else {
        $sql = "SELECT * FROM eventos WHERE estado = 'abierto'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    }

    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['rol' => $rol, 'cursos' => $cursos]);
} else {
    // Usuario no logueado → mostrar todos los cursos, sin botones
    $sql = "SELECT * FROM eventos WHERE estado = 'abierto'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['rol' => null, 'cursos' => $cursos]);
}
