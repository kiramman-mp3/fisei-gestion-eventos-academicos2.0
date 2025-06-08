<?php
require_once '../session.php';
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$uid = $_SESSION['uid'] ?? '';
$uname = $_SESSION['uname'] ?? '';
$uemail = $_SESSION['uemail'] ?? '';
$urol = $_SESSION['urol'] ?? '';

$sesion_activa = !empty($uid) && !empty($uname) && !empty($uemail) && !empty($urol);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Solicitar ayuda</title>
    <link rel="stylesheet" href="../css/styles.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
<div class="alert-success">
    ✅ ¡Tu solicitud ha sido enviada!
</div>
<?php endif; ?>

<header class="top-header">
    <div class="site-name">Universidad Técnica de Ambato</div>
</header>

<main class="card">
    <h1>Solicitar ayuda</h1>

    <form action="enviar_ayuda.php" method="POST">
        <label>
            <span class="rojo">Título:</span><br />
            <input type="text" name="titulo" required>
        </label>

        <label style="display:block;margin-top:16px;">
            <span class="rojo">Descripción del problema:</span><br />
            <textarea name="descripcion" rows="5" required></textarea>
        </label>

        <fieldset class="user-info" style="margin-top:20px;">
            <legend>Información del usuario</legend>
            <input type="text" name="uid" value="<?= htmlspecialchars($uid) ?>" readonly>
            <input type="text" name="uname" value="<?= htmlspecialchars($uname) ?>" readonly>
            <input type="email" name="uemail" value="<?= htmlspecialchars($uemail) ?>" readonly>
            <input type="text" name="urol" value="<?= htmlspecialchars($urol) ?>" readonly>
        </fieldset>

        <div class="actions">
            <button type="reset" class="btn cancelar">Cancelar</button>
            <button type="submit" class="btn enviar">Enviar</button>
        </div>
    </form>
</main>

    <footer class="footer-expandido mt-5">
        <div class="footer-container">
            <div class="footer-section">
                <h5>Sobre el sistema</h5>
                <ul>
                    <li><a href="../informativo/que_es_eventos.html"><i class="fa-solid fa-circle-question"></i> ¿Qué es Eventos FISEI?</a></li>
                    <li><a href="../informativo/manual_usuario.html"><i class="fa-solid fa-book"></i> Manual de usuario</a></li>
                    <li><a href="../informativo/versiones.html"><i class="fa-solid fa-code-branch"></i> Versiones</a></li>
                    <li><a href="../informativo/nosotros.html"><i class="fa-solid fa-user-group"></i> Créditos</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h5>Soporte</h5>
                <ul>
                    <li><a href="../informativo/preguntas_frecuentes.html"><i class="fa-solid fa-circle-info"></i> Preguntas frecuentes</a></li>
                    <li><a href="../informativo/reportar_error.html"><i class="fa-solid fa-bug"></i> Reportar un error</a></li>
                    <li><a href="../informativo/solicitar_ayuda.html"><i class="fa-solid fa-headset"></i> Solicitar ayuda</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h5>Legal</h5>
                <ul>
                    <li><a href="../informativo/terminos_uso.html"><i class="fa-solid fa-file-contract"></i> Términos de uso</a></li>
                    <li><a href="../informativo/politica_privacidad.html"><i class="fa-solid fa-user-shield"></i> Política de privacidad</a></li>
                    <li><a href="../informativo/licencia.html"><i class="fa-solid fa-scroll"></i> Licencia</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h5>FISEI - UTA</h5>
                <p>Facultad de Ingeniería en Sistemas,<br> Electrónica e Industrial</p>
                <div class="footer-social">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            © <?= date('Y') ?> FISEI - Universidad Técnica de Ambato. Todos los derechos reservados.
        </div>
    </footer>

</body>
</html>
