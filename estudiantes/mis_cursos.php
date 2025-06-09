<?php
require_once '../session.php';
include('../sql/conexion.php');

<?php
  require_once '../session.php';
  $nombre = getUserName();
  $apellido = getUserLastname();
?>

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$usuarioId = getUserId();
$cris = new Conexion();
$conexion = $cris->conectar();

$stmt = $conexion->prepare("
    SELECT e.id AS evento_id, e.nombre_evento, e.fecha_inicio, e.fecha_fin, e.ponentes,
           e.horas, i.estado, i.nota, i.asistencia, i.comprobante_pago
    FROM inscripciones i
    JOIN eventos e ON i.evento_id = e.id
    WHERE i.usuario_id = ?
");
$stmt->execute([$usuarioId]);
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mis Cursos</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <h1>Mis Cursos Inscritos</h1>

            <?php if (count($cursos) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Curso</th>
                                <th>Fechas</th>
                                <th>Ponente</th>
                                <th>Horas</th>
                                <th>Nota</th>
                                <th>Asistencia</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cursos as $curso): ?>
                                <tr>
                                    <td><?= htmlspecialchars($curso['nombre_evento']) ?></td>
                                    <td><?= $curso['fecha_inicio'] ?> al <?= $curso['fecha_fin'] ?></td>
                                    <td><?= htmlspecialchars($curso['ponentes']) ?></td>
                                    <td><?= $curso['horas'] ?></td>
                                    <td><?= is_null($curso['nota']) ? '-' : $curso['nota'] ?></td>
                                    <td><?= is_null($curso['asistencia']) ? '-' : $curso['asistencia'] . '%' ?></td>
                                    <td><?= htmlspecialchars($curso['estado']) ?></td>
                                    <td class="text-center">
                                        <?php
                                        $aptoCertificado = (
                                            $curso['estado'] === 'Pagado' &&
                                            $curso['nota'] >= 7 &&
                                            $curso['asistencia'] >= 70
                                        );
                                        ?>
                                        <?php if ($aptoCertificado): ?>
                                            <a href="certificado_pdf.php?evento_id=<?= $curso['evento_id'] ?>" target="_blank"
                                                class="btn btn-outline-primary btn-sm">
                                                Generar PDF
                                            </a>
                                        <?php elseif ($curso['estado'] === 'En espera de orden de pago'): ?>
                                            <form method="POST" action="subir_comprobante.php" enctype="multipart/form-data"
                                                class="d-inline">
                                                <input type="hidden" name="evento_id" value="<?= $curso['evento_id'] ?>">
                                                <input type="file" name="comprobante" accept="application/pdf,image/*" required
                                                    style="display: none;" onchange="this.form.submit()">
                                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                                    onclick="this.previousElementSibling.click()">
                                                    Subir comprobante
                                                </button>
                                            </form>
                                        <?php elseif (!empty($curso['comprobante_pago'])): ?>
                                            <a href="<?= $curso['comprobante_pago'] ?>" target="_blank"
                                                class="btn btn-white btn-sm">
                                                Ver comprobante
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">No disponible</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">No estás inscrito en ningún curso.</p>
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