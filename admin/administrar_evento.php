<?php
require_once '../session.php';
include('../sql/conexion.php');

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}



  $nombre = getUserName();
  $apellido = getUserLastname();


$id = (int) $_GET['id'];
$conexion = (new Conexion())->conectar();

// Datos del evento
$stmt = $conexion->prepare("
    SELECT e.*, t.nombre AS tipo_evento, c.nombre AS categoria 
    FROM eventos e
    JOIN tipos_evento t ON e.tipo_evento_id = t.id
    JOIN categorias_evento c ON e.categoria_id = c.id
    WHERE e.id = ?
");
$stmt->execute([$id]);
$evento = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$evento) {
    echo "<h2>Evento no encontrado.</h2>";
    exit;
}

// Requisitos
$reqStmt = $conexion->prepare("SELECT descripcion FROM requisitos_evento WHERE evento_id = ?");
$reqStmt->execute([$id]);
$requisitos = $reqStmt->fetchAll(PDO::FETCH_ASSOC);

// Inscripciones
$insStmt = $conexion->prepare("
    SELECT i.id, i.estado, i.nota, i.asistencia,
           e.nombre, e.apellido, e.cedula, e.correo
    FROM inscripciones i
    JOIN estudiantes e ON i.usuario_id = e.id
    WHERE i.evento_id = ?
");
$insStmt->execute([$id]);
$inscritos = $insStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Administrar Evento</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>

<header class="top-header d-flex justify-content-between align-items-center px-4 py-2 shadow-sm --maroon">
    <div class="d-flex align-items-center">
    <a href="../index.php">
  <img src="../resource/logo-universidad-tecnica-de-ambato.webp" alt="Logo institucional" style="height: 50px;">
</a>
      <div class="site-name ms-3 fw-bold">Gestión de Eventos Académicos - FISEI</div>
    </div>

    <div class="d-flex align-items-center gap-3">
      <?php if (isLoggedIn()): ?>
        <a href="../perfil.php" class="fw-semibold text-white text-decoration-none">
  Hola, <?= htmlspecialchars(getUserName()) ?> <?= htmlspecialchars(getUserLastname()) ?>
</a>

        <a href="../logout.php" class="btn btn-white"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
      <?php else: ?>
        <a href="../login.php" class="btn btn-white"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
        <a href="../registro.php" class="btn btn-white"><i class="fas fa-user-plus"></i> Registrarse</a>
      <?php endif; ?>
    </div>
  </header>

    <main>
        <div class="card">
            <h1><?= htmlspecialchars($evento['nombre_evento']) ?></h1>

            <div class="mb-4">
                <p><strong>Tipo:</strong> <?= htmlspecialchars($evento['tipo_evento']) ?></p>
                <p><strong>Categoría:</strong> <?= htmlspecialchars($evento['categoria']) ?></p>
                <p><strong>Fechas:</strong> <?= $evento['fecha_inicio'] ?> al <?= $evento['fecha_fin'] ?></p>
                <p><strong>Inscripciones:</strong> <?= $evento['fecha_inicio_inscripciones'] ?> al
                    <?= $evento['fecha_fin_inscripciones'] ?></p>
                <p><strong>Horas académicas:</strong> <?= $evento['horas'] ?></p>
                <p><strong>Ponente:</strong> <?= htmlspecialchars($evento['ponentes']) ?></p>
                <p><strong>Cupos disponibles:</strong> <?= $evento['cupos'] ?></p>
                <p><strong>Estado:</strong> <?= $evento['estado'] ?></p>
            </div>
            <div class="text-end mb-3">
                <a href="pdf_evento.php?id=<?= $id ?>" target="_blank" class="btn btn-outline-secondary">
                    <i class="fa fa-file-pdf"></i> Imprimir PDF
                </a>
            </div>


            <h4>Requisitos del Evento</h4>
            <?php if (count($requisitos) > 0): ?>
                <ul class="list-group mb-4">
                    <?php foreach ($requisitos as $req): ?>
                        <li class="list-group-item"><?= htmlspecialchars($req['descripcion']) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">Este evento no tiene requisitos registrados.</p>
            <?php endif; ?>

            <h4>Inscritos (<?= count($inscritos) ?>)</h4>
            <?php if (count($inscritos) > 0): ?>
                <form id="formNotas" method="POST" action="actualizar_notas.php">
                    <input type="hidden" name="evento_id" value="<?= $id ?>">

                    <div class="table-responsive">
                        <table class="table table-custom">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Estudiante</th>
                                    <th>Cédula</th>
                                    <th>Correo</th>
                                    <th>Estado</th>
                                    <th>Nota</th>
                                    <th>Asistencia (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inscritos as $i => $ins): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($ins['nombre'] . ' ' . $ins['apellido']) ?></td>
                                        <td><?= htmlspecialchars($ins['cedula']) ?></td>
                                        <td><?= htmlspecialchars($ins['correo']) ?></td>
                                        <td><?= htmlspecialchars($ins['estado']) ?></td>
                                        <td>
                                            <input type="number" name="notas[<?= $ins['id'] ?>]" class="form-control"
                                                value="<?= is_null($ins['nota']) ? '' : $ins['nota'] ?>" step="0.01" min="0"
                                                max="10">
                                        </td>
                                        <td>
                                            <input type="number" name="asistencias[<?= $ins['id'] ?>]" class="form-control"
                                                value="<?= is_null($ins['asistencia']) ? '' : $ins['asistencia'] ?>" step="0.01"
                                                min="0" max="100">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            <?php else: ?>
                <p class="text-muted">No hay inscritos en este evento.</p>
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
        <li><a href="../formulario/solicitud_cambios.php"><i class="fa-solid fa-bug"></i> Reportar un error</a></li>
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