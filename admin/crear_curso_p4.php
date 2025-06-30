<?php
require_once '../session.php';
require_once '../sql/conexion.php';

if (!isLoggedIn() || getUserRole() !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['nuevo_curso'])) {
    header('Location: crear_curso_p1.php');
    exit;
}

$errores = [];
$requisito_tipo = $_POST['tipo'] ?? '';
$requisito_descripcion = trim($_POST['descripcion'] ?? '');
$requisito_campo = $_POST['campo'] ?? '';
$accion = $_POST['accion'] ?? null;

if (!isset($_SESSION['nuevo_curso']['requisitos'])) {
    $_SESSION['nuevo_curso']['requisitos'] = [];
}

// Agregar requisito
if ($accion === 'agregar') {
    if ($requisito_tipo === 'documento') {
        if (empty($requisito_campo)) {
            $errores[] = "Debe seleccionar un documento del perfil.";
        } else {
            $_SESSION['nuevo_curso']['requisitos'][] = [
                'tipo' => 'documento',
                'descripcion' => ucfirst($requisito_campo),
                'campo' => $requisito_campo
            ];
        }
    } elseif ($requisito_tipo === 'texto') {
        if (strlen($requisito_descripcion) < 5) {
            $errores[] = "El requisito de texto debe tener al menos 5 caracteres.";
        } else {
            $_SESSION['nuevo_curso']['requisitos'][] = [
                'tipo' => 'texto',
                'descripcion' => $requisito_descripcion,
                'campo' => null
            ];
        }
    } else {
        $errores[] = "Debe seleccionar un tipo de requisito válido.";
    }
}

// Eliminar requisito
if ($accion === 'eliminar' && isset($_POST['indice'])) {
    $indice = (int) $_POST['indice'];
    if (isset($_SESSION['nuevo_curso']['requisitos'][$indice])) {
        array_splice($_SESSION['nuevo_curso']['requisitos'], $indice, 1);
    }
}

// Continuar
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
    <h2>Crear Curso - Paso 4: Requisitos</h2>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errores as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="border rounded p-3 mb-4 bg-light">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="tipo" class="form-label">Tipo de requisito</label>
                <select name="tipo" id="tipo" class="form-select" required onchange="actualizarCampos()">
                    <option value="">Seleccionar</option>
                    <option value="documento">Documento del perfil</option>
                    <option value="texto">Texto libre</option>
                </select>
            </div>

            <div class="col-md-4" id="campoDocumento" style="display:none;">
                <label for="campo" class="form-label">Documento requerido</label>
                <select name="campo" id="campo" class="form-select">
                    <option value="">Seleccione</option>
                    <option value="ruta_cedula">Cédula</option>
                    <option value="ruta_matricula">Matrícula</option>
                    <option value="ruta_papeleta">Papeleta de votación</option>
                </select>
            </div>

            <div class="col-md-4" id="campoTexto" style="display:none;">
                <label for="descripcion" class="form-label">Descripción del requisito</label>
                <input type="text" name="descripcion" id="descripcion" class="form-control" placeholder="Ej. Carta de motivación">
            </div>

            <div class="col-md-2">
                <input type="hidden" name="accion" value="agregar">
                <button type="submit" class="btn btn-success w-100">Agregar</button>
            </div>
        </div>
    </form>

    <h5>Requisitos actuales:</h5>
    <?php if (empty($_SESSION['nuevo_curso']['requisitos'])): ?>
        <p class="text-muted">No se han agregado requisitos aún.</p>
    <?php else: ?>
        <ul class="list-group mb-4">
            <?php foreach ($_SESSION['nuevo_curso']['requisitos'] as $i => $req): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($req['descripcion']) ?> 
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

<script>
    function actualizarCampos() {
        const tipo = document.getElementById('tipo').value;
        document.getElementById('campoDocumento').style.display = tipo === 'documento' ? 'block' : 'none';
        document.getElementById('campoTexto').style.display = tipo === 'texto' ? 'block' : 'none';
    }
</script>
</body>
</html>
