<?php
require_once '../session.php';
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

<?php
  require_once '../session.php';
  $nombre = getUserName();
  $apellido = getUserLastname();
?>

include_once '../sql/conexion.php';

$cris = new Conexion();
$conn = $cris->conectar();

// Obtener todas las resoluciones con info de la solicitud
$sql = "SELECT r.*, s.titulo, s.tipo, s.fecha 
        FROM resoluciones r
        JOIN solicitudes s ON r.id_solicitud = s.id
        ORDER BY r.fecha_resolucion DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$resoluciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Todas las Resoluciones</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .res-card {
            border: 1px solid var(--gray-200);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 20px;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .res-card h4 {
            margin-top: 0;
            color: var(--maroon-dark);
        }

        .res-info {
            margin-bottom: 8px;
        }

        .btn-editar {
            margin-top: 12px;
            display: inline-block;
        }
    </style>
</head>

<body>
    <header class="top-header d-flex justify-content-between align-items-center px-4 py-2 shadow-sm --maroon">
        <div class="d-flex align-items-center">
     <a href="../index.php">
      <img src="../resource/logo-uta.png" alt="Logo institucional" style="height: 50px;">
        </a>
     <div class="site-name ms-3 fw-bold">Gestión de Eventos Académicos - FISEI</div>
     </div>
        <div class="d-flex align-items-center gap-3">
      <?php if (isLoggedIn()): ?>
      <span class="fw-semibold">Hola, <?= htmlspecialchars($nombre) ?> <?= htmlspecialchars($apellido) ?></span>
      <a href="../logout.php" class="btn btn-white"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
      <?php else: ?>
      <a href="../login.php" class="btn btn-white"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
      <a href="../registro.php" class="btn btn-white"><i class="fas fa-user-plus"></i> Registrarse</a>
     <?php endif; ?>
        </div>
    </header>

    <main class="card">
        <h1>Todas las Resoluciones</h1>

        <?php if (count($resoluciones) > 0): ?>
            <?php foreach ($resoluciones as $res): ?>
                <div class="res-card">
                    <h4><?= htmlspecialchars($res['titulo']) ?> (<?= $res['tipo'] ?>)</h4>
                    <div class="res-info"><strong>Estado:</strong> <?= $res['estado'] ?></div>
                    <div class="res-info"><strong>Prioridad:</strong> <?= $res['prioridad'] ?></div>
                    <div class="res-info"><strong>Comentario:</strong><br><?= nl2br(htmlspecialchars($res['comentario'])) ?>
                    </div>
                    <div class="res-info"><strong>Fecha de resolución:</strong>
                        <?= date('d-m-Y', strtotime($res['fecha_resolucion'])) ?></div>
                    <a href="editar_resolucion.php?id=<?= $res['id'] ?>" class="btn enviar btn-editar"
                        style="text-decoration: none;">
                        <i class="fa-solid fa-pen-to-square"></i> Editar
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay resoluciones registradas.</p>
        <?php endif; ?>
    </main>
    
    <footer class="footer-expandido mt-5">
  <div class="footer-container">
    <div class="footer-section">
      <h5>Sobre el sistema</h5>
      <ul>
        <li><a href="../informativo/que_es_eventos.php"><i class="fa-solid fa-circle-question"></i> ¿Qué es Eventos FISEI?</a></li>
        <li><a href="../informativo/manual_usuario.php"><i class="fa-solid fa-book"></i> Manual de usuario</a></li>
        <li><a href="../informativo/versiones.php"><i class="fa-solid fa-code-branch"></i> Versiones</a></li>
        <li><a href="../informativo/nosotros.php"><i class="fa-solid fa-user-group"></i> Créditos</a></li>
      </ul>
    </div>
    <div class="footer-section">
      <h5>Soporte</h5>
      <ul>
        <li><a href="../informativo/preguntas_frecuentes.php"><i class="fa-solid fa-circle-info"></i> Preguntas frecuentes</a></li>
        <li><a href="../formulario/solictud_cambios.php"><i class="fa-solid fa-bug"></i> Reportar un error</a></li>
        <li><a href="../formulario/solicitar_ayuda.php"><i class="fa-solid fa-headset"></i> Solicitar ayuda</a></li>
      </ul>
    </div>
    <div class="footer-section">
      <h5>Legal</h5>
      <ul>
        <li><a href="../legal/terminos_uso.php"><i class="fa-solid fa-file-contract"></i> Términos de uso</a></li>
        <li><a href="../legal/politica_privacidad.php"><i class="fa-solid fa-user-shield"></i> Política de privacidad</a></li>
        <li><a href="../legal/licencia.php"><i class="fa-solid fa-scroll"></i> Licencia</a></li>
      </ul>
    </div>
    <div class="footer-section">
      <h5>FISEI - UTA</h5>
      <p>Facultad de Ingeniería en Sistemas,<br> Electrónica e Industrial</p>
      <div class="footer-social">
        <a href="https://www.facebook.com/UTAFISEI"><i class="fab fa-facebook-f"></i></a>
        <a href="https://www.instagram.com/fisei_uta"><i class="fab fa-instagram"></i></a>
        <a href="https://www.linkedin.com/pub/dir?firstName=Fisei&lastName=uta&trk=people-guest_people-search-bar_search-submit"><i class="fab fa-linkedin-in"></i></a>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    © <?= date('Y') ?> FISEI - Universidad Técnica de Ambato. Todos los derechos reservados.
  </div>
</footer>

</body>

</html>