<?php
require_once '../session.php';
include('../sql/conexion.php');

if (!isLoggedIn()) {
  header('Location: login.php');
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <header class="ctt-header">
    <div class="top-bar d-flex justify-content-between align-items-center p-3 bg-danger text-white">
      <div class="logo">
        <img src="../uploads/logo.png" alt="Logo FISEI" style="height: 60px;">
      </div>
      <div class="top-links">
        <a href="javascript:history.back()" class="btn btn-light">
          <i class="fa-solid fa-arrow-left"></i> Regresar al Dashboard
        </a>
      </div>
    </div>
  </header>

  <main class="container my-5">
    <div class="card p-4 shadow-sm">
      <h2 class="mb-4"><?= htmlspecialchars($evento['nombre_evento']) ?></h2>

      <div class="mb-4">
        <p><strong>Tipo:</strong> <?= htmlspecialchars($evento['tipo_evento']) ?></p>
        <p><strong>Categoría:</strong> <?= htmlspecialchars($evento['categoria']) ?></p>
        <p><strong>Fechas:</strong> <?= $evento['fecha_inicio'] ?> al <?= $evento['fecha_fin'] ?></p>
        <p><strong>Inscripciones:</strong> <?= $evento['fecha_inicio_inscripciones'] ?> al <?= $evento['fecha_fin_inscripciones'] ?></p>
        <p><strong>Horas académicas:</strong> <?= $evento['horas'] ?></p>
        <p><strong>Ponente:</strong> <?= htmlspecialchars($evento['ponentes']) ?></p>
        <p><strong>Cupos disponibles:</strong> <?= $evento['cupos'] ?></p>
        <p><strong>Estado:</strong> <?= $evento['estado'] ?></p>
      </div>

      <div class="text-end mb-4">
        <a href="pdf_evento.php?id=<?= $id ?>" target="_blank" class="btn btn-outline-secondary">
          <i class="fa fa-file-pdf"></i> Imprimir PDF
        </a>
      </div>

      <h5>Requisitos del Evento</h5>
      <?php if (count($requisitos) > 0): ?>
        <ul class="list-group mb-4">
          <?php foreach ($requisitos as $req): ?>
            <li class="list-group-item"><?= htmlspecialchars($req['descripcion']) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="text-muted">Este evento no tiene requisitos registrados.</p>
      <?php endif; ?>

      <h5 class="mt-5">Inscritos (<?= count($inscritos) ?>)</h5>

      <?php if (count($inscritos) > 0): ?>
        <form id="formNotas" method="POST" action="actualizar_notas.php">
          <input type="hidden" name="evento_id" value="<?= $id ?>">

          <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
              <thead class="table-light">
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
                             value="<?= is_null($ins['nota']) ? '' : $ins['nota'] ?>" step="0.01" min="0" max="10">
                    </td>
                    <td>
                      <input type="number" name="asistencias[<?= $ins['id'] ?>]" class="form-control"
                             value="<?= is_null($ins['asistencia']) ? '' : $ins['asistencia'] ?>" step="0.01" min="0" max="100">
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div class="text-end mt-3">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
          </div>
        </form>
      <?php else: ?>
        <p class="text-muted">No hay inscritos en este evento.</p>
      <?php endif; ?>
    </div>
  </main>
</body>

</html>
