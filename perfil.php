<?php
require_once 'session.php';
include('sql/conexion.php');

if (!isLoggedIn()) {
  header('Location: login.php');
  exit;
}

$correo = getUserEmail();

// Validación de carrera protegida
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Solo institucional y sin carrera puede elegir una
  if ($_SESSION['user']['tipo'] === 'institucional' && empty($_SESSION['user']['carrera'])) {
    $carrera = $_POST['carrera'] ?? null;
  } else {
    $carrera = $_SESSION['user']['carrera'];
  }

  $uploadDir = 'uploads/documentos/' . str_replace('@', '_', $correo) . '/';
  if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

  $rutas = [
    'cedula_path' => $_SESSION['user']['cedula_path'],
    'papeleta_path' => $_SESSION['user']['papeleta_path'],
    'matricula_path' => $_SESSION['user']['matricula_path']
  ];

  foreach ($rutas as $key => &$ruta) {
    $campo = str_replace('_path', '', $key);
    if (isset($_FILES[$campo]) && $_FILES[$campo]['error'] === 0) {
      $file = $_FILES[$campo];
      $destino = $uploadDir . $campo . '_' . basename($file['name']);
      if (move_uploaded_file($file['tmp_name'], $destino)) {
        $ruta = $destino;
      }
    }
  }

  $stmt = $conexion->prepare("UPDATE estudiantes SET carrera = ?, cedula_path = ?, papeleta_path = ?, matricula_path = ? WHERE correo = ?");
  $stmt->bind_param("sssss", $carrera, $rutas['cedula_path'], $rutas['papeleta_path'], $rutas['matricula_path'], $correo);
  $stmt->execute();

  // Refrescar sesión
  $query = $conexion->prepare("SELECT * FROM estudiantes WHERE correo = ?");
  $query->bind_param("s", $correo);
  $query->execute();
  $_SESSION['user'] = $query->get_result()->fetch_assoc();

  header("Location: perfil.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mi Perfil</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <h2 class="text-center text-primary mb-4">Mi Perfil</h2>
    <div class="card p-4 shadow mx-auto" style="max-width: 600px;">
      <p><strong>Nombre:</strong> <?= htmlspecialchars(getUserName()) . ' ' . htmlspecialchars(getUserLastname()) ?></p>
      <p><strong>Cédula:</strong> <?= htmlspecialchars($_SESSION['user']['cedula']) ?></p>
      <p><strong>Correo:</strong> <?= htmlspecialchars(getUserEmail()) ?></p>
      <p><strong>Tipo:</strong> <?= $_SESSION['user']['tipo'] === 'institucional' ? 'Universitario UTA' : 'Público' ?></p>
      <p><strong>Género:</strong> <?= htmlspecialchars($_SESSION['user']['genero']) ?></p>
      <p><strong>Fecha de nacimiento:</strong> <?= htmlspecialchars($_SESSION['user']['fecha_nacimiento']) ?></p>

      <form method="POST" enctype="multipart/form-data" class="mt-4">
        <?php if ($_SESSION['user']['tipo'] === 'institucional' && empty($_SESSION['user']['carrera'])): ?>
          <div class="mb-3">
            <label class="form-label">Seleccionar carrera:</label>
            <select name="carrera" class="form-select" required>
              <option value="">--Seleccione--</option>
              <option value="Software">Ingeniería en Software</option>
              <option value="Electrónica">Electrónica</option>
              <option value="Industrial">Industrial</option>
            </select>
          </div>
        <?php elseif ($_SESSION['user']['tipo'] === 'publico'): ?>
          <p class="alert alert-warning">Tu correo es público. No puedes seleccionar una carrera.</p>
          <input type="hidden" name="carrera" value="">
        <?php else: ?>
          <p><strong>Carrera:</strong> <?= htmlspecialchars($_SESSION['user']['carrera']) ?: 'No asignada' ?></p>
          <input type="hidden" name="carrera" value="<?= htmlspecialchars($_SESSION['user']['carrera']) ?>">
        <?php endif; ?>

        <div class="mb-3">
          <label class="form-label">Subir o reemplazar cédula escaneada:</label>
          <input type="file" name="cedula" class="form-control" accept="application/pdf,image/*">
          <?php if ($_SESSION['user']['cedula_path']): ?>
            <p class="mt-1"><a href="<?= $_SESSION['user']['cedula_path'] ?>" target="_blank">Ver archivo actual</a></p>
          <?php endif; ?>
        </div>

        <div class="mb-3">
          <label class="form-label">Subir o reemplazar papeleta de votación:</label>
          <input type="file" name="papeleta" class="form-control" accept="application/pdf,image/*">
          <?php if ($_SESSION['user']['papeleta_path']): ?>
            <p class="mt-1"><a href="<?= $_SESSION['user']['papeleta_path'] ?>" target="_blank">Ver archivo actual</a></p>
          <?php endif; ?>
        </div>

        <div class="mb-3">
          <label class="form-label">Subir o reemplazar matrícula:</label>
          <input type="file" name="matricula" class="form-control" accept="application/pdf,image/*">
          <?php if ($_SESSION['user']['matricula_path']): ?>
            <p class="mt-1"><a href="<?= $_SESSION['user']['matricula_path'] ?>" target="_blank">Ver archivo actual</a></p>
          <?php endif; ?>
        </div>

        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
      </form>
    </div>

    <div class="text-center mt-4">
      <a href="mis_cursos.php" class="btn btn-outline-primary">Ver mis cursos</a>
      <a href="logout.php" class="btn btn-outline-danger ms-2">Cerrar sesión</a>
    </div>
  </div>
</body>
</html>
