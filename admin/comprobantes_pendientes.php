<?php
require_once '../session.php';
include('../sql/conexion.php');

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

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

<header class="top-header">
  <img src="ruta/logo.png" alt="Logo institucional">
  <div class="site-name">Aprobación de Comprobantes</div>
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

</body>
</html>
