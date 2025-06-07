<?php
require_once '../session.php';
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$titulo = $_POST['titulo'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$uid = $_POST['uid'] ?? '';
$uname = $_POST['uname'] ?? '';
$uemail = $_POST['uemail'] ?? '';
$urol = $_POST['urol'] ?? '';

// Validación básica
if (empty($titulo) || empty($descripcion)) {
    echo "❌ Todos los campos obligatorios deben estar llenos.";
    exit();
}

// Configura el correo de destino (admin o soporte)
$destinatario = "soporte@fisei.uta.edu.ec";
$asunto = "Nueva solicitud de ayuda: $titulo";

$mensaje = "
    Se ha enviado una nueva solicitud de ayuda desde el sistema Eventos FISEI.

    🧾 Título: $titulo
    📝 Descripción: $descripcion

    👤 Usuario: $uname ($urol)
    📧 Correo: $uemail
    🆔 UID: $uid
";

$cabeceras = "From: Eventos FISEI <noreply@fisei.uta.edu.ec>\r\n";
$cabeceras .= "Reply-To: $uemail\r\n";
$cabeceras .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Enviar correo
$enviado = mail($destinatario, $asunto, $mensaje, $cabeceras);

if ($enviado) {
    header('Location: solicitar_ayuda.php?success=1');
    exit();
} else {
    echo "❌ No se pudo enviar el correo. Intenta más tarde.";
}
