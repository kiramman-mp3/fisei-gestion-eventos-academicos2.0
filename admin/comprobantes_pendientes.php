<?php
require_once '../session.php';
include('../sql/conexion.php');

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

<?php
  require_once '../session.php';
  $nombre = getUserName();
  $apellido = getUserLastname();
?>

$cris = new Conexion();
$conexion = $cris->conectar();

$stmt = $conexion->prepare("
    SELECT i.id, i.usuario_id, i.evento_id, i.comprobante_pago, i.estado,
           e.nombre_evento,
           es.nombre, es.apellido, es.cedula
    FROM inscripciones i
    JOIN eventos e ON i.evento_id = e.id
    JOIN estudiantes es ON i.usuario_id = es.id
    WHERE i.estado = 'Esperando aprobación del admin'
");
$stmt->execute();
$pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Comprobantes Pendientes</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
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

<main>
  <div class="card">
    <h1>Comprobantes Pendientes</h1>

    <?php if (count($pendientes) > 0): ?>
      <table class="table table-custom">
        <thead>
          <tr>
            <th>Estudiante</th>
            <th>Cédula</th>
            <th>Curso</th>
            <th>Archivo</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pendientes as $row): ?>
            <tr>
              <td><?= htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) ?></td>
              <td><?= htmlspecialchars($row['cedula']) ?></td>
              <td><?= htmlspecialchars($row['nombre_evento']) ?></td>
              <td>
                <a href="<?= $row['comprobante_pago'] ?>" target="_blank" class="btn btn-white btn-sm">Ver archivo</a>
              </td>
              <td>
                <form action="aprobar_comprobante.php" method="POST" class="d-inline">
                  <input type="hidden" name="inscripcion_id" value="<?= $row['id'] ?>">
                  <button name="accion" value="aprobar" class="btn btn-primary btn-sm">Aprobar</button>
                  <button name="accion" value="rechazar" class="btn btn-outline-secondary btn-sm">Rechazar</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="text-muted">No hay comprobantes pendientes.</p>
    <?php endif; ?>
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
