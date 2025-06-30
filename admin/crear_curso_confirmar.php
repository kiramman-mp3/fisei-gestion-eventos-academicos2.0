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

$conexion = (new Conexion())->conectar();
$errores = [];
$curso = $_SESSION['nuevo_curso'];

try {
    $stmtTipo = $conexion->prepare("SELECT nombre FROM tipos_evento WHERE id = ?");
    $stmtTipo->execute([$curso['tipo_evento_id']]);
    $nombreTipo = $stmtTipo->fetchColumn() ?: 'Sin tipo';

    $stmtCat = $conexion->prepare("SELECT nombre FROM categorias_evento WHERE id = ?");
    $stmtCat->execute([$curso['categoria_id']]);
    $nombreCategoria = $stmtCat->fetchColumn() ?: 'Sin categoría';
} catch (Exception $e) {
    $nombreTipo = 'Error';
    $nombreCategoria = 'Error';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conexion->beginTransaction();

        $stmt = $conexion->prepare("INSERT INTO eventos 
            (nombre_evento, tipo_evento_id, categoria_id, ponentes, descripcion, fecha_inicio, fecha_fin, fecha_inicio_inscripciones, fecha_fin_inscripciones, horas, cupos, ruta_imagen, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $curso['nombre_evento'],
            $curso['tipo_evento_id'],
            $curso['categoria_id'],
            $curso['ponentes'],
            $curso['descripcion'],
            $curso['fecha_inicio'],
            $curso['fecha_fin'],
            $curso['fecha_inicio_inscripciones'],
            $curso['fecha_fin_inscripciones'],
            $curso['horas'],
            $curso['cupos'],
            $curso['ruta_imagen'] ?? null,
            'abierto'
        ]);

        $evento_id = $conexion->lastInsertId();

        if (!empty($curso['requisitos'])) {
            $stmtReq = $conexion->prepare("INSERT INTO requisitos_evento (evento_id, descripcion, tipo, campo_estudiante) VALUES (?, ?, ?, ?)");
            foreach ($curso['requisitos'] as $req) {
                if (is_array($req) && isset($req['descripcion'])) {
                    $stmtReq->execute([
                        $evento_id,
                        $req['descripcion'],
                        $req['tipo'] ?? 'archivo',
                        $req['campo_estudiante'] ?? null
                    ]);
                }
            }
        }

        $conexion->commit();
        unset($_SESSION['nuevo_curso']);
        header('Location: ../ver_cursos.php');
        exit;
    } catch (Exception $e) {
        $conexion->rollBack();
        $errores[] = "Error al guardar el curso: " . $e->getMessage();
    }
}

$nombreUsuario = getUserName();
$apellidoUsuario = getUserLastname();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Curso - Confirmar datos</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous">
    <style>
        .confirmacion-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px 40px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        .confirmacion-container img {
            display: block;
            margin: 20px auto;
            max-height: 300px;
            max-width: 100%;
            object-fit: contain;
        }

        .confirmacion-container p {
            margin-bottom: 6px;
        }

        .confirmacion-container ul {
            padding-left: 20px;
        }

        .acciones-finales {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
    </style>
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

<div class="confirmacion-container">
    <h3 class="text-center mb-4">Crear Curso - Confirmar datos</h3>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errores as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <h4 class="text-center"><?= htmlspecialchars($curso['nombre_evento']) ?></h4>
    <p><strong>Tipo:</strong> <?= htmlspecialchars($nombreTipo) ?></p>
    <p><strong>Categoría:</strong> <?= htmlspecialchars($nombreCategoria) ?></p>
    <p><strong>Fechas:</strong> <?= $curso['fecha_inicio'] ?> al <?= $curso['fecha_fin'] ?></p>
    <p><strong>Inscripciones:</strong> <?= $curso['fecha_inicio_inscripciones'] ?> al <?= $curso['fecha_fin_inscripciones'] ?></p>
    <p><strong>Cupos:</strong> <?= $curso['cupos'] ?></p>
    <p><strong>Horas:</strong> <?= $curso['horas'] ?></p>
    <p><strong>Ponente(s):</strong> <?= htmlspecialchars($curso['ponentes']) ?></p>
    <p><strong>Descripción:</strong> <?= htmlspecialchars($curso['descripcion']) ?></p>

    <?php if (!empty($curso['ruta_imagen'])): ?>
        <p><strong>Imagen:</strong></p>
        <img src="<?= htmlspecialchars($curso['ruta_imagen']) ?>" alt="Previa del curso" class="img-thumbnail">
    <?php endif; ?>

    <p><strong>Requisitos:</strong></p>
    <?php if (isset($curso['requisitos']) && is_array($curso['requisitos']) && count($curso['requisitos']) > 0): ?>
        <ul>
            <?php foreach ($curso['requisitos'] as $r): ?>
                <li><?= htmlspecialchars(is_array($r) && isset($r['descripcion']) ? $r['descripcion'] : (string)$r) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">No se registraron requisitos.</p>
    <?php endif; ?>

    <form method="POST" class="acciones-finales">
        <a href="crear_curso_p4.php" class="btn btn-secondary">&laquo; Volver</a>
        <button type="submit" class="btn btn-success">Confirmar y guardar</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
