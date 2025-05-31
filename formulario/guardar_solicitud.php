<?php
include_once '../sql/conexion.php';

$cris = new Conexion();
$conn = $cris->conectar();

$titulo = $_POST['titulo'] ?? '';
$fecha = $_POST['fecha'] ?? '';
$tipo = $_POST['tipo'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$justificacion = $_POST['justificacion'] ?? '';
$contexto = $_POST['contexto'] ?? '';
$uid = $_POST['uid'] ?? '';
$uname = $_POST['uname'] ?? '';
$uemail = $_POST['uemail'] ?? '';
$urol = $_POST['urol'] ?? '';

$imagenRuta = "";
if (isset($_FILES['captura']) && $_FILES['captura']['error'] === UPLOAD_ERR_OK) {
    $nombreArchivo = basename($_FILES["captura"]["name"]);
    $directorioDestino = "../uploads/";
    if (!is_dir($directorioDestino)) {
        mkdir($directorioDestino, 0755, true);
    }
    $rutaFinal = $directorioDestino . time() . "_" . $nombreArchivo;

    if (move_uploaded_file($_FILES["captura"]["tmp_name"], $rutaFinal)) {
        $imagenRuta = $rutaFinal;
    }
}

try {
    $stmt = $conn->prepare("INSERT INTO solicitudes 
        (titulo, fecha, tipo, descripcion, justificacion, contexto, captura, uid, uname, uemail, urol) 
        VALUES (:titulo, :fecha, :tipo, :descripcion, :justificacion, :contexto, :captura, :uid, :uname, :uemail, :urol)");

    $stmt->execute([
        ':titulo' => $titulo,
        ':fecha' => $fecha,
        ':tipo' => $tipo,
        ':descripcion' => $descripcion,
        ':justificacion' => $justificacion,
        ':contexto' => $contexto,
        ':captura' => $imagenRuta,
        ':uid' => $uid,
        ':uname' => $uname,
        ':uemail' => $uemail,
        ':urol' => $urol
    ]);

    // Redirigir con éxito
    header("Location: solicitud_cambios.php?success=1");
    exit;
} catch (PDOException $e) {
    echo "❌ Error al guardar la solicitud: " . $e->getMessage();
}
?>
