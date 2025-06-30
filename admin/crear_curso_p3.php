<?php
require_once '../session.php';
require_once '../sql/conexion.php';

if (!isLoggedIn() || getUserRole() !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

if (!isset($_SESSION)) session_start();
$nombre = getUserName();
$apellido = getUserLastname();

$errores = [];
$descripcion = '';
$ruta_imagen = $_SESSION['nuevo_curso']['ruta_imagen'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descripcion = trim($_POST['descripcion'] ?? '');

    if (strlen($descripcion) < 10) {
        $errores[] = "La descripción debe tener al menos 10 caracteres.";
    }

    // Procesar imagen si se cargó
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['imagen']['tmp_name'];
        $nombreArchivo = basename($_FILES['imagen']['name']);
        $ext = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $permitidas)) {
            $errores[] = "Formato de imagen no permitido. Solo JPG, PNG, WEBP.";
        } else {
            $destino = "../uploads/" . uniqid('evento_') . "." . $ext;
            if (!move_uploaded_file($tmp, $destino)) {
                $errores[] = "Error al subir la imagen.";
            } else {
                $ruta_imagen = $destino;
            }
        }
    }

    if (empty($errores)) {
        $_SESSION['nuevo_curso']['descripcion'] = $descripcion;
        if ($ruta_imagen) {
            $_SESSION['nuevo_curso']['ruta_imagen'] = $ruta_imagen;
        }
        header('Location: crear_curso_p4.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Curso - Paso 3</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous">
</head>
<body class="bg-light">
<header class="top-header d-flex justify-content-between align-items-center px-4 py-2 shadow-sm" style="background-color: var(--maroon);">
  <div class="d-flex align-items-center">
    <a href="../ver_cursos.php">
      <img src="../resource/logo-universidad-tecnica-de-ambato.webp" alt="Logo institucional" style="height: 50px;">
    </a>
    <div class="site-name ms-3 fw-bold text-white">Gestión de Eventos Académicos - FISEI</div>
  </div>
  <div class="d-flex align-items-center gap-3">
    <span class="fw-semibold text-white">Hola, <?= htmlspecialchars($nombre) ?> <?= htmlspecialchars($apellido) ?></span>
    <a href="../logout.php" class="btn btn-white rounded-pill">
      <i class="fas fa-sign-out-alt"></i> Cerrar sesión
    </a>
  </div>
</header>

<div class="container mt-5">
    <h2>Crear Curso - Paso 3: Imagen y Descripción</h2>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errores as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción del evento</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="5" required><?= htmlspecialchars($descripcion) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen del evento (opcional)</label>
            <input type="file" name="imagen" id="imagen" class="form-control">
            <?php if ($ruta_imagen): ?>
                <div class="mt-2">
                    <p>Imagen actual:</p>
                    <img src="<?= $ruta_imagen ?>" alt="Previa" class="img-thumbnail" style="max-width: 300px;">
                </div>
            <?php endif; ?>
        </div>

        <div class="d-flex justify-content-between">
            <a href="crear_curso_p2.php" class="btn btn-secondary">&laquo; Anterior</a>
            <button type="submit" class="btn btn-primary">Siguiente &raquo;</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
