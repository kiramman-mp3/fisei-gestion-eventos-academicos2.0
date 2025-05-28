<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel Administrador - Eventos FISEI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

    <header class="top-header">
        <img src="../resource/logo-uta.png" alt="Logo UTA">
        <div class="site-name">Eventos Académicos FISEI</div>
    </header>

    <main class="card-custom">
        <div class="text-center mb-4">
            <h1>Bienvenido Administrador</h1>
            <p class="text-muted">Selecciona una acción para gestionar los cursos y eventos.</p>
        </div>

        <div class="list-group">
            <a href="crearEventos.php" class="list-group-item list-group-item-action">
                <i class="fa-solid fa-plus-circle me-2"></i> Crear Curso/Evento
            </a>
            <a href="subirRequisitos.php" class="list-group-item list-group-item-action">
                <i class="fa-solid fa-upload me-2"></i> Subir o Editar Requisitos
            </a>
            <a href="notasAsistencia.php" class="list-group-item list-group-item-action">
                <i class="fa-solid fa-clipboard-list me-2"></i> Registrar Notas y Asistencia
            </a>
            <a href="validarPagos.php" class="list-group-item list-group-item-action">
                <i class="fa-solid fa-money-check-alt me-2"></i> Validar Pagos
            </a>
        </div>
    </main>

    <footer class="footer-expandido">
        <div class="footer-container">
            <div class="footer-section">
                <h5>Sobre el sistema</h5>
                <ul>
                    <li><a href="#"><i class="fa-solid fa-circle-question"></i> ¿Qué es Eventos FISEI?</a></li>
                    <li><a href="#"><i class="fa-solid fa-book"></i> Manual de usuario</a></li>
                    <li><a href="#"><i class="fa-solid fa-code-branch"></i> Versiones</a></li>
                    <li><a href="#"><i class="fa-solid fa-user-group"></i> Créditos</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h5>Soporte</h5>
                <ul>
                    <li><a href="#"><i class="fa-solid fa-circle-info"></i> Preguntas frecuentes</a></li>
                    <li><a href="../formulario/solicitud_cambios.php"><i class="fa-solid fa-bug"></i> Reportar un error</a></li>
                    <li><a href="#"><i class="fa-solid fa-headset"></i> Solicitar ayuda</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h5>Legal</h5>
                <ul>
                    <li><a href="#"><i class="fa-solid fa-file-contract"></i> Términos de uso</a></li>
                    <li><a href="#"><i class="fa-solid fa-user-shield"></i> Política de privacidad</a></li>
                    <li><a href="#"><i class="fa-solid fa-scroll"></i> Licencia</a></li>
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
