<?php
require_once '../session.php';


include_once '../sql/conexion.php';
$cris = new Conexion();
$conn = $cris->conectar();

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID inválido.";
    exit;
}
  $nombre = getUserName();
  $apellido = getUserLastname();


// Obtener la resolución y la solicitud asociada
$stmt = $conn->prepare("
    SELECT r.*, s.titulo, s.tipo, s.descripcion, s.justificacion, s.contexto, s.fecha, s.uname, s.urol, s.captura 
    FROM resoluciones r 
    JOIN solicitudes s ON r.id_solicitud = s.id 
    WHERE r.id = ?
");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "Resolución no encontrada.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Resolución</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .detalle-item {
            margin-bottom: 12px;
        }

        .form-label {
            display: block;
            margin-top: 20px;
            font-weight: bold;
        }

        .form-control,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid var(--gray-200);
            border-radius: 6px;
        }

        .solicitud-img {
            max-width: 300px;
            border-radius: 6px;
            margin-top: 10px;
            border: 1px solid var(--gray-200);
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
        <h1><i class="fa-solid fa-pen-to-square icono"></i> Editar Resolución</h1>

        <section style="margin-bottom: 40px;">
            <h2>Datos de la solicitud</h2>
            <input type="hidden" name="id" value="<?= $data['id'] ?>">
            <div class="detalle-item"><strong>Título:</strong> <?= htmlspecialchars($data['titulo']) ?></div>
            <div class="detalle-item"><strong>Tipo:</strong> <?= $data['tipo'] ?></div>
            <div class="detalle-item"><strong>Fecha:</strong> <?= date('d-m-Y', strtotime($data['fecha'])) ?></div>
            <div class="detalle-item">
                <strong>Descripción:</strong><br><?= nl2br(htmlspecialchars($data['descripcion'])) ?>
            </div>
            <div class="detalle-item">
                <strong>Justificación:</strong><br><?= nl2br(htmlspecialchars($data['justificacion'])) ?>
            </div>
            <div class="detalle-item"><strong>Contexto:</strong><br><?= nl2br(htmlspecialchars($data['contexto'])) ?>
            </div>
            <div class="detalle-item"><strong>Usuario:</strong> <?= $data['uname'] ?> (<?= $data['urol'] ?>)</div>

            <?php if ($data['captura']): ?>
                <div class="detalle-item">
                    <strong>Captura de pantalla:</strong><br>
                    <img src="<?= $data['captura'] ?>" class="solicitud-img" alt="Captura">
                </div>
            <?php endif; ?>
        </section>

        <h2>Modificar resolución</h2>

        <form action="guardar_resolucion.php" method="POST">
            <input type="hidden" name="id" value="<?= $data['id'] ?>">

            <label class="form-label">Prioridad:</label>
            <select name="prioridad" required>
                <option value="Alta" <?= $data['prioridad'] === 'Alta' ? 'selected' : '' ?>>Alta</option>
                <option value="Media" <?= $data['prioridad'] === 'Media' ? 'selected' : '' ?>>Media</option>
                <option value="Baja" <?= $data['prioridad'] === 'Baja' ? 'selected' : '' ?>>Baja</option>
            </select>

            <label class="form-label">Estado:</label>
            <select name="estado" required>
                <option value="En revisión" <?= $data['estado'] === 'En revisión' ? 'selected' : '' ?>>En revisión</option>
                <option value="Aprobado" <?= $data['estado'] === 'Aprobado' ? 'selected' : '' ?>>Aprobado</option>
                <option value="Rechazado" <?= $data['estado'] === 'Rechazado' ? 'selected' : '' ?>>Rechazado</option>
                <option value="Terminado" <?= $data['estado'] === 'Terminado' ? 'selected' : '' ?>>Terminado</option>
            </select>

            <label class="form-label">Comentario:</label>
            <textarea name="comentario" rows="4" required><?= htmlspecialchars($data['comentario']) ?></textarea>

            <button type="submit" class="btn enviar" style="margin-top: 24px;">
                <i class="fa-solid fa-save"></i> Guardar cambios
            </button>
        </form>
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