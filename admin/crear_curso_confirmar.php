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
                        $req['campo'] ?? null
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
    <link rel="stylesheet" href="../css/gestion-css.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous">
    <style>
        /* Variables de color - Si no están ya definidas globalmente en gestion-css.css */
        /* Si ya tienes estas variables en gestion-css.css, puedes omitir esta sección en el <style> */
        :root {
            --primary-color: #B02A37;
            /* Rojo para el título y el botón de Confirmar */
            --primary-color-dark: #8E222C;
            /* Rojo más oscuro para hover */
            --secondary-color: #6c757d;
            /* Gris para el botón de Volver */
            --secondary-color-dark: #5a6268;
            /* Gris más oscuro para hover */
            --success-color: #28a745;
            /* Verde para el botón de Agregar/Guardar */
            --success-color-dark: #218838;
            /* Verde más oscuro para hover */
            --danger-color: #dc3545;
            /* Rojo para errores y botón Eliminar */
            --danger-color-dark: #c82333;
            /* Rojo más oscuro para hover */
            --text-color: #333;
            /* Color de texto general */
            --light-bg: #f8f9fa;
            /* Fondo claro para formularios */
            --border-color: #e9ecef;
            /* Color de borde general */
            --input-border-color: #ced4da;
            /* Color de borde de inputs */
            --input-focus-border-color: #80bdff;
            /* Color de borde de input al enfocar */
            --box-shadow-light: 0 3px 12px rgba(0, 0, 0, 0.08);
            /* Sombra ligera para el contenedor */
            --box-shadow-medium: 0 2px 5px rgba(0, 0, 0, 0.1);
            /* Sombra media para header */
            --header-bg: #900;
            /* Granate oscuro para el header */
        }

        /* Estilos generales del cuerpo (si no están ya definidos en gestion-css.css) */
        /* Si ya tienes estos estilos en gestion-css.css, puedes omitir esta sección en el <style> */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            /* Un gris muy claro para el fondo general */
            color: var(--text-color);
        }   


        /* --- Contenedor de Confirmación (Estilos específicos para esta página) --- */
        .confirm-details-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px 40px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: var(--box-shadow-light);
        }

        .confirm-details-container h3 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.2rem;
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 12px;
            display: inline-block;
            width: auto;
            left: 50%;
            transform: translateX(-50%);
            position: relative;
        }

        .confirm-details-container h4 {
            color: var(--text-color);
            text-align: center;
            margin-top: 20px;
            margin-bottom: 25px;
            font-size: 1.8rem;
            font-weight: bold;
        }

        .confirm-details-container p {
            margin-bottom: 8px;
            font-size: 1.05rem;
            line-height: 1.5;
            color: #444;
        }

        .confirm-details-container p strong {
            color: var(--primary-color-dark);
            display: inline-block;
            min-width: 120px;
        }

        .confirm-details-container ul {
            padding-left: 30px;
            margin-bottom: 20px;
            list-style-type: disc;
            color: #444;
        }

        .confirm-details-container ul li {
            margin-bottom: 5px;
            font-size: 1rem;
            line-height: 1.4;
        }

        .confirm-details-container .course-image-preview {
            display: block;
            margin: 20px auto 30px auto;
            max-height: 350px;
            max-width: 100%;
            object-fit: contain;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .confirm-details-container .text-muted-custom {
            color: #888;
            font-style: italic;
            margin-top: 10px;
            margin-bottom: 20px;
            text-align: left;
            font-size: 1rem;
        }

        /* Estilos para el mensaje de error de PHP (mantener aquí si son muy específicos, sino mover a gestion-css.css) */
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 25px;
            font-size: 0.95rem;
        }

        .alert-danger ul {
            margin: 0;
            padding-left: 25px;
            list-style-type: disc;
        }

        /* --- Acciones Finales (Botones) --- */
        .final-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .button-back {
            background-color: var(--secondary-color);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.15rem;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.2s ease, transform 0.1s ease, box-shadow 0.2s ease;
        }

        .button-back:hover {
            background-color: var(--secondary-color-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .button-confirm-save {
            background-color: #c82333;
            color: #fff;
            padding: 15px 30px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.15rem;
            font-weight: 600;
            transition: background-color 0.2s ease, transform 0.1s ease, box-shadow 0.2s ease;
        }

        .button-confirm-save:hover {
            background-color: #8d1f2d;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Media queries para responsividad */
        @media (max-width: 768px) {
            .confirm-details-container {
                padding: 20px;
                margin: 20px auto;
            }

            .confirm-details-container h3 {
                font-size: 1.8rem;
                margin-bottom: 20px;
                padding-bottom: 8px;
            }

            .confirm-details-container h4 {
                font-size: 1.5rem;
            }

            .confirm-details-container p {
                font-size: 0.95rem;
            }

            .confirm-details-container p strong {
                min-width: 90px;
            }

            .final-actions {
                flex-direction: column;
                gap: 15px;
            }

            .button-back,
            .button-confirm-save {
                width: 100%;
                text-align: center;
                padding: 12px 20px;
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>

    <header class="ctt-header">
        <div class="top-bar">
            <div class="logo">
                <img src="../uploads/logo.png" alt="Logo CTT">
            </div>
            <div class="top-links">
                <div class="link-box">
                    <i class="fa-solid fa-arrow-left"></i>
                    <div>
                        <span class="title">Regresar</span>
                        <br>
                        <a href="javascript:history.back()">Página anterior</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="confirm-details-container">
        <h3>Crear Curso - Confirmar datos</h3>

        <?php if (!empty($errores)): ?>
            <div class="alert-danger">
                <ul>
                    <?php foreach ($errores as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <h4><?= htmlspecialchars($curso['nombre_evento']) ?></h4>
        <p><strong>Tipo:</strong> <?= htmlspecialchars($nombreTipo) ?></p>
        <p><strong>Categoría:</strong> <?= htmlspecialchars($nombreCategoria) ?></p>
        <p><strong>Fechas:</strong> <?= $curso['fecha_inicio'] ?> al <?= $curso['fecha_fin'] ?></p>
        <p><strong>Inscripciones:</strong> <?= $curso['fecha_inicio_inscripciones'] ?> al
            <?= $curso['fecha_fin_inscripciones'] ?>
        </p>
        <p><strong>Cupos:</strong> <?= $curso['cupos'] ?></p>
        <p><strong>Horas:</strong> <?= $curso['horas'] ?></p>
        <p><strong>Ponente(s):</strong> <?= htmlspecialchars($curso['ponentes']) ?></p>
        <p><strong>Descripción:</strong> <?= htmlspecialchars($curso['descripcion']) ?></p>

        <?php if (!empty($curso['ruta_imagen'])): ?>
            <p><strong>Imagen:</strong></p>
            <img src="<?= htmlspecialchars($curso['ruta_imagen']) ?>" alt="Previa del curso" class="course-image-preview">
        <?php endif; ?>

        <p><strong>Requisitos:</strong></p>
        <?php if (isset($curso['requisitos']) && is_array($curso['requisitos']) && count($curso['requisitos']) > 0): ?>
            <ul>
                <?php foreach ($curso['requisitos'] as $r): ?>
                    <li><?= htmlspecialchars(is_array($r) && isset($r['descripcion']) ? $r['descripcion'] : (string) $r) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted-custom">No se registraron requisitos.</p>
        <?php endif; ?>

        <form method="POST" class="final-actions">
            <a href="crear_curso_p4.php" class="button-back">&laquo; Volver</a>
            <button type="submit" class="button-confirm-save">Confirmar y guardar</button>
        </form>
    </div>

</body>

</html>