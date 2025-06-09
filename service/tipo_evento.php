<?php
require_once '../session.php';

require_once '../sql/conexion.php';
$cris = new Conexion();
$conn = $cris->conectar();
$action = $_GET['action'] ?? '';

if ($action === 'listar') {
    $stmt = $conn->query("SELECT * FROM tipos_evento");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

if ($action === 'crear') {
    $stmt = $conn->prepare("INSERT INTO tipos_evento (nombre) VALUES (?)");
    $stmt->execute([$_POST['nombre']]);
    echo json_encode(['mensaje' => 'Tipo de evento creado']);
}

if ($action === 'editar') {
    $stmt = $conn->prepare("UPDATE tipos_evento SET nombre=? WHERE id=?");
    $stmt->execute([$_POST['nombre'], $_POST['id']]);
    echo json_encode(['mensaje' => 'Tipo de evento actualizado']);
}
?>
