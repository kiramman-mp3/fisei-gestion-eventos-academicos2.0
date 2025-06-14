<?php
require_once '../session.php';
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

  $nombre = getUserName();
  $apellido = getUserLastname();

include_once '../sql/conexion.php';
$cris = new Conexion();
$conn = $cris->conectar();

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID inválido";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM solicitudes WHERE id = ?");
$stmt->execute([$id]);
$solicitud = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$solicitud) {
    echo "Solicitud no encontrada.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalle de Solicitud</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .detalle-item {
            margin-bottom: 16px;
        }

        .detalle-item span {
            font-weight: bold;
            color: var(--gray-900);
        }

        .seccion-resolucion {
            margin-top: 40px;
            border-top: 2px solid var(--gray-200);
            padding-top: 32px;
        }

        .seccion-resolucion label {
            display: block;
            margin-top: 16px;
            font-weight: bold;
            color: var(--gray-900);
        }

        .seccion-resolucion textarea,
        .seccion-resolucion select {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid var(--gray-200);
            margin-top: 8px;
        }

        .solicitud-img {
            max-width: 300px;
            height: auto;
            display: block;
            margin-top: 16px;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }


        .btn.enviar {
            margin-top: 24px;
        }

        .icono {
            margin-right: 6px;
            color: var(--maroon-dark);
        }
    </style>
</head>

<body>

<header class="top-header d-flex justify-content-between align-items-center px-4 py-2 shadow-sm --maroon">
    <div class="d-flex align-items-center">
    <a href="index.php">
  <img src="../resource/logo-universidad-tecnica-de-ambato.webp" alt="Logo institucional" style="height: 50px;">
</a>
      <div class="site-name ms-3 fw-bold">Gestión de Eventos Académicos - FISEI</div>
    </div>

    <div class="d-flex align-items-center gap-3">
      <?php if (isLoggedIn()): ?>
        <a href="perfil.php" class="fw-semibold text-white text-decoration-none">
  Hola, <?= htmlspecialchars(getUserName()) ?> <?= htmlspecialchars(getUserLastname()) ?>
</a>

        <a href="logout.php" class="btn btn-white"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-white"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
        <a href="registro.php" class="btn btn-white"><i class="fas fa-user-plus"></i> Registrarse</a>
      <?php endif; ?>
    </div>
  </header>

    <main class="card">

        <h1>Detalle de la Solicitud</h1>
        <input type="hidden" name="id_solicitud" value="<?= $solicitud['id'] ?>">
        <div class="detalle-item"><span>Título:</span> <?= htmlspecialchars($solicitud['titulo']) ?></div>
        <div class="detalle-item"><span>Fecha:</span> <?= date("d-m-Y", strtotime($solicitud['fecha'])) ?></div>
        <div class="detalle-item"><span>Tipo:</span> <?= $solicitud['tipo'] ?></div>
        <div class="detalle-item"><span>Descripción:</span><br><?= nl2br(htmlspecialchars($solicitud['descripcion'])) ?>
        </div>
        <div class="detalle-item">
            <span>Justificación:</span><br><?= nl2br(htmlspecialchars($solicitud['justificacion'])) ?>
        </div>
        <div class="detalle-item"><span>Contexto:</span><br><?= nl2br(htmlspecialchars($solicitud['contexto'])) ?></div>
        <div class="detalle-item"><span>Usuario:</span> <?= $solicitud['uname'] ?> (<?= $solicitud['urol'] ?>)</div>

        <?php if ($solicitud['captura']): ?>
            <div class="detalle-item">
                <span>Captura de pantalla:</span><br>
                <img src="<?= $solicitud['captura'] ?>" alt="Captura" class="solicitud-img">
            </div>
        <?php endif; ?>

        <div class="seccion-resolucion">
            <h2><i class="fa-solid fa-pen-to-square icono"></i> Resolver solicitud</h2>
            <form method="POST" action="guardar_resolucion.php">
                <input type="hidden" name="id_solicitud" value="<?= $solicitud['id'] ?>">

                <label>Prioridad:</label>
                <select name="prioridad" required>
                    <option value="" disabled selected>Seleccione</option>
                    <option value="Alta">Alta</option>
                    <option value="Media">Media</option>
                    <option value="Baja">Baja</option>
                </select>

                <label>Comentario:</label>
                <textarea name="comentario" rows="4" required placeholder="Describe tu evaluación..."></textarea>

                <label>Estado:</label>
                <select name="estado" required>
                    <option value="En revisión">En revisión</option>
                    <option value="Aprobado">Aprobado</option>
                    <option value="Rechazado">Rechazado</option>
                </select>

                <button type="submit" class="btn enviar"><i class="fa-solid fa-check"></i> Guardar resolución</button>
            </form>
        </div>
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