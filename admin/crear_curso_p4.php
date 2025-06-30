<?php
require_once '../session.php';
require_once '../sql/conexion.php';

if (!isLoggedIn() || getUserRole() !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['nuevo_curso'])) {
    header('Location: crear_curso_p1.php');
    exit;
}

$errores = [];
$requisito_nuevo = trim($_POST['requisito'] ?? '');
$accion = $_POST['accion'] ?? null;

// Inicializar arreglo si no está
if (!isset($_SESSION['nuevo_curso']['requisitos'])) {
    $_SESSION['nuevo_curso']['requisitos'] = [];
}

// Agregar requisito
if ($accion === 'agregar' && $requisito_nuevo !== '') {
    if (strlen($requisito_nuevo) < 5) {
        $errores[] = "El requisito debe tener al menos 5 caracteres.";
    } else {
        $_SESSION['nuevo_curso']['requisitos'][] = $requisito_nuevo;
    }
}

// Eliminar requisito
if ($accion === 'eliminar' && isset($_POST['indice'])) {
    $indice = (int) $_POST['indice'];
    if (isset($_SESSION['nuevo_curso']['requisitos'][$indice])) {
        array_splice($_SESSION['nuevo_curso']['requisitos'], $indice, 1);
    }
}

// Continuar al siguiente paso
if ($accion === 'continuar') {
    header('Location: crear_curso_confirmar.php');
    exit;
}

$nombreUsuario = getUserName();
$apellidoUsuario = getUserLastname();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Curso - Paso 4</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
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
    <h2>Crear Curso - Paso 4: Requisitos (opcional)</h2>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errores as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="mb-4 d-flex align-items-end gap-3">
        <div class="flex-grow-1">
            <label for="requisito" class="form-label">Nuevo requisito</label>
            <input type="text" name="requisito" id="requisito" class="form-control" placeholder="Ej: Haber aprobado Fundamentos" required>
        </div>
        <input type="hidden" name="accion" value="agregar">
        <button type="submit" class="btn btn-success">Agregar</button>
    </form>

    <h5>Requisitos actuales:</h5>
    <?php if (empty($_SESSION['nuevo_curso']['requisitos'])): ?>
        <p class="text-muted">No se han agregado requisitos aún.</p>
    <?php else: ?>
        <ul class="list-group mb-4">
            <?php foreach ($_SESSION['nuevo_curso']['requisitos'] as $i => $req): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($req) ?>
                    <form method="POST" class="m-0">
                        <input type="hidden" name="indice" value="<?= $i ?>">
                        <input type="hidden" name="accion" value="eliminar">
                        <button class="btn btn-danger btn-sm">Eliminar</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div class="d-flex justify-content-between">
        <a href="crear_curso_p3.php" class="btn btn-secondary">&laquo; Volver</a>
        <form method="POST" class="m-0">
            <input type="hidden" name="accion" value="continuar">
            <button type="submit" class="btn btn-primary">Confirmar &raquo;</button>
        </form>
    </div>
</div>

<script src="https://kit.fontawesome.com/2c36e9b7b1.js" crossorigin="anonymous"></script>
</body>
</html>
