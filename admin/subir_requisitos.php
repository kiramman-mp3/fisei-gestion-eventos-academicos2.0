<?php
include '../session.php';
include '../sql/conexion.php';
$conn = (new Conexion())->conectar();

$inscripcion_id = $_POST['inscripcion_id'];
$evento_id = $_POST['evento_id'];

$stmt = $conn->prepare("SELECT id FROM requisitos_evento WHERE evento_id = ?");
$stmt->execute([$evento_id]);
$requisitos = $stmt->fetchAll(PDO::FETCH_COLUMN);

$ruta_guardado = "../uploads/requisitos/";
if (!file_exists($ruta_guardado)) {
    mkdir($ruta_guardado, 0777, true);
}

// Procesar archivos subidos
foreach ($requisitos as $req_id) {
    $input_name = "requisito_" . $req_id;
    if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] === UPLOAD_ERR_OK) {
        $nombre_archivo = basename($_FILES[$input_name]['name']);
        $destino = $ruta_guardado . uniqid() . "_" . $nombre_archivo;

        move_uploaded_file($_FILES[$input_name]['tmp_name'], $destino);

        $stmt2 = $conn->prepare("INSERT INTO requisitos_inscripcion (inscripcion_id, requisito_id, archivo) VALUES (?, ?, ?)");
        $stmt2->execute([$inscripcion_id, $req_id, $destino]);
    }
}

// Redirigir a una página de confirmación o de vuelta al evento
if (isset($_POST['redirect_url'])) {
    header("Location: " . $_POST['redirect_url']);
} else {
    header("Location: ../admin/administrar_evento.php?id=$evento_id");
}
exit;
