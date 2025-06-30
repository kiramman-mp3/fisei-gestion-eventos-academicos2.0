<?php
require_once '../session.php';
require_once '../sql/conexion.php';

if (!isLoggedIn() || getUserRole() !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

if (!isset($_SESSION['nuevo_curso'])) {
    header('Location: crear_curso_p1.php');
    exit;
}

$conexion = (new Conexion())->conectar();
$errores = [];

$tiposEvento = $conexion->query("SELECT id, nombre FROM tipos_evento")->fetchAll(PDO::FETCH_ASSOC);
$categoriasEvento = $conexion->query("SELECT id, nombre FROM categorias_evento")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_evento_id = $_POST['tipo_evento_id'] ?? '';
    $categoria_id = $_POST['categoria_id'] ?? '';
    $modalidad = $_POST['modalidad'] ?? null;
    $descripcion = $_POST['descripcion'] ?? null;

    if (!$tipo_evento_id || !$categoria_id) {
        $errores[] = "Debe seleccionar tipo y categoría del evento.";
    }

    if (empty($errores)) {
        $_SESSION['nuevo_curso']['tipo_evento_id'] = $tipo_evento_id;
        $_SESSION['nuevo_curso']['categoria_id'] = $categoria_id;
        if ($modalidad) $_SESSION['nuevo_curso']['modalidad'] = $modalidad;
        if ($descripcion) $_SESSION['nuevo_curso']['descripcion_adicional'] = $descripcion;

        header('Location: crear_curso_p3.php');
        exit;
    }
}

$nombreUsuario = getUserName();
$apellidoUsuario = getUserLastname();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Curso - Paso 2</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous">
</head>
<body>

<header class="top-header d-flex justify-content-between align-items-center px-4 py-2 shadow-sm" style="background-color: var(--maroon);">
  <div class="d-flex align-items-center">
    <a href="../ver_cursos.php">
      <img src="../resource/logo-universidad-tecnica-de-ambato.webp" alt="Logo institucional" style="height: 50px;">
    </a>
    <div class="site-name ms-3 fw-bold text-white">Gestión de Eventos Académicos - FISEI</div>
  </div>
  <div class="d-flex align-items-center gap-3">
    <span class="fw-semibold text-white">Hola, <?= htmlspecialchars($nombreUsuario) ?> <?= htmlspecialchars($apellidoUsuario) ?></span>
    <a href="../logout.php" class="btn btn-white rounded-pill">
      <i class="fas fa-sign-out-alt"></i> Cerrar sesión
    </a>
  </div>
</header>

<div class="container mt-5">
    <h2>Crear Curso - Paso 2: Detalles adicionales</h2>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errores as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="tipo_evento_id" class="form-label">Tipo de evento</label>
            <select name="tipo_evento_id" id="tipo_evento_id" class="form-select" required>
                <option value="">Seleccione un tipo</option>
                <?php foreach ($tiposEvento as $tipo): ?>
                    <option value="<?= $tipo['id'] ?>"><?= htmlspecialchars($tipo['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="categoria_id" class="form-label">Categoría</label>
            <select name="categoria_id" id="categoria_id" class="form-select" required>
                <option value="">Seleccione una categoría</option>
                <?php foreach ($categoriasEvento as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="modalidad" class="form-label">Modalidad (opcional)</label>
            <select name="modalidad" id="modalidad" class="form-select">
                <option value="">Seleccione</option>
                <option value="presencial">Presencial</option>
                <option value="virtual">Virtual</option>
                <option value="híbrido">Híbrido</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción corta (opcional)</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="3"></textarea>
        </div>

        <div class="d-flex justify-content-between">
            <a href="crear_curso_p1.php" class="btn btn-secondary">&laquo; Volver</a>
            <button type="submit" class="btn btn-danger">Siguiente &raquo;</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
