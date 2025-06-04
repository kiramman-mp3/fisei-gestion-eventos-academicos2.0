<?php
require_once '../session.php';
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include_once '../sql/conexion.php';
$cris = new Conexion();
$conn = $cris->conectar();

$sql = "SELECT id, titulo, fecha, tipo, descripcion FROM solicitudes ORDER BY fecha DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Solicitudes de Cambio</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>

    </style>
</head>

<body>
    <header class="top-header">
        <div class="site-name">Universidad<br>Técnica de Ambato</div>
    </header>

    <main class="card">
        <h1>Solicitudes de Cambios</h1>

        <?php if (count($solicitudes) > 0): ?>
            <div class="card-grid">
                <?php foreach ($solicitudes as $sol): ?>
                    <div class="card-item">
                        <h3 style="color: var(--maroon-dark);"><?= htmlspecialchars($sol['titulo']) ?></h3>
                        <p><strong>Fecha:</strong>
                            <?= ($sol['fecha'] !== '0000-00-00') ? date("d-m-Y", strtotime($sol['fecha'])) : 'Sin fecha' ?></p>
                        <p><strong>Tipo:</strong> <?= $sol['tipo'] ?></p>
                        <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($sol['descripcion'])) ?></p>
                        <a href="detalle_solicitud.php?id=<?= $sol['id'] ?>" class="btn enviar"
                            style="margin-top: 10px; text-decoration: none;">
                            <i class="fa-solid fa-eye"></i> Ver detalles
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No hay solicitudes registradas.</p>
        <?php endif; ?>
    </main>



    <footer class="footer-expandido">
        <div class="footer-container">
            <div class="footer-section">
                <h5>Sobre el sistema</h5>
                <ul>
                    <li><a href="#"><i class="fa-solid fa-circle-question"></i> ¿Qué es Eventos FISEI?</a></li>
                    <li><a href="#"><i class="fa-solid fa-book"></i> Manual de usuario</a></li>
                    <li><a href="#"><i class="fa-solid fa-code-branch"></i> Versiones</a></li>
                    <li><a href="../informativo/nosotros.php"><i class="fa-solid fa-user-group"></i> Créditos</a>
                    </li>
                </ul>
            </div>
            <div class="footer-section">
                <h5>Soporte</h5>
                <ul>
                    <li><a href="#"><i class="fa-solid fa-circle-info"></i> Preguntas frecuentes</a></li>
                    <li><a href="../formulario/solicitud_cambios.php"><i class="fa-solid fa-bug"></i> Reportar un
                            error</a></li>
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