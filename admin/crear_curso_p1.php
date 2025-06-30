<?php
require_once '../session.php';
require_once '../sql/conexion.php';

if (!isLoggedIn() || getUserRole() !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

if (!isset($_SESSION['nuevo_curso'])) {
    $_SESSION['nuevo_curso'] = [];
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';
    $fecha_inicio_insc = $_POST['fecha_inicio_inscripciones'] ?? '';
    $fecha_fin_insc = $_POST['fecha_fin_inscripciones'] ?? '';
    $cupos = (int) ($_POST['cupos'] ?? 0);
    $ponentes = trim($_POST['ponentes'] ?? '');
    $horas = (int) ($_POST['horas'] ?? 0);

    if ($nombre === '') $errores[] = "El nombre del evento es obligatorio.";
    if (!$fecha_inicio || !$fecha_fin || $fecha_inicio > $fecha_fin) $errores[] = "Fechas del evento inválidas.";
    if (!$fecha_inicio_insc || !$fecha_fin_insc || $fecha_inicio_insc > $fecha_fin_insc) $errores[] = "Fechas de inscripción inválidas.";
    if ($cupos <= 0) $errores[] = "Debe haber al menos 1 cupo.";
    if ($horas <= 0) $errores[] = "Debe haber al menos 1 hora académica.";

    if (empty($errores)) {
        $_SESSION['nuevo_curso'] = [
            'nombre_evento' => $nombre,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'fecha_inicio_inscripciones' => $fecha_inicio_insc,
            'fecha_fin_inscripciones' => $fecha_fin_insc,
            'cupos' => $cupos,
            'ponentes' => $ponentes,
            'horas' => $horas
        ];
        header('Location: crear_curso_p2.php');
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
    <title>Crear Curso - Paso 1</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous">
</head>
<body>

<!-- HEADER -->
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

<!-- MAIN -->
<div class="container mt-5">
    <h2>Crear Curso - Paso 1: Información general</h2>

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
            <label for="nombre" class="form-label">Nombre del curso</label>
            <input type="text" class="form-control" name="nombre" id="nombre" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
                <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="fecha_fin" class="form-label">Fecha de fin</label>
                <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="fecha_inicio_inscripciones" class="form-label">Inicio inscripciones</label>
                <input type="date" class="form-control" name="fecha_inicio_inscripciones" id="fecha_inicio_inscripciones" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="fecha_fin_inscripciones" class="form-label">Fin inscripciones</label>
                <input type="date" class="form-control" name="fecha_fin_inscripciones" id="fecha_fin_inscripciones" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="cupos" class="form-label">Cupos disponibles</label>
                <input type="number" class="form-control" name="cupos" id="cupos" min="1" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="horas" class="form-label">Horas académicas</label>
                <input type="number" class="form-control" name="horas" id="horas" min="1" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="ponentes" class="form-label">Ponente(s)</label>
                <input type="text" class="form-control" name="ponentes" id="ponentes" required>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-danger">Siguiente »</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
