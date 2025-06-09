<?php
require_once '../session.php';
include('../sql/conexion.php');

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$usuarioId = getUserId();
$eventoId = $_POST['evento_id'] ?? null;

if (!is_numeric($eventoId) || !isset($_FILES['comprobante'])) {
    exit('Datos inválidos.');
}

$comprobante = $_FILES['comprobante'];
$permitidos = ['application/pdf', 'image/jpeg', 'image/png'];

if (!in_array($comprobante['type'], $permitidos) || $comprobante['error'] !== UPLOAD_ERR_OK) {
    exit('Archivo inválido.');
}

// Subir archivo
$dir = '../uploads/comprobantes/' . $usuarioId . '/';
if (!file_exists($dir))
    mkdir($dir, 0777, true);

$ext = pathinfo($comprobante['name'], PATHINFO_EXTENSION);
$nombreFinal = 'comprobante_' . $eventoId . '.' . $ext;
$destino = $dir . $nombreFinal;

if (!move_uploaded_file($comprobante['tmp_name'], $destino)) {
    exit('Error al subir el archivo.');
}

// Guardar ruta en base de datos
$conexion = (new Conexion())->conectar();
$stmt = $conexion->prepare("
    UPDATE inscripciones 
    SET comprobante_pago = ?, estado = 'Esperando aprobación del admin' 
    WHERE usuario_id = ? AND evento_id = ?
");
$stmt->execute([$destino, $usuarioId, $eventoId]);

header("Location: mis_cursos.php");
exit;
