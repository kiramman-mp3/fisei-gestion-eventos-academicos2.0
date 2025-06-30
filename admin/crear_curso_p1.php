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

    if ($nombre === '')
        $errores[] = "El nombre del evento es obligatorio.";
    if (!$fecha_inicio || !$fecha_fin || $fecha_inicio > $fecha_fin)
        $errores[] = "Fechas del evento inválidas.";
    if (!$fecha_inicio_insc || !$fecha_fin_insc || $fecha_inicio_insc > $fecha_fin_insc)
        $errores[] = "Fechas de inscripción inválidas.";
    if ($cupos <= 0)
        $errores[] = "Debe haber al menos 1 cupo.";
    if ($horas <= 0)
        $errores[] = "Debe haber al menos 1 hora académica.";

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
    <link rel="stylesheet" href="../css/gestion-css.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous">
    <style>
        .main-content-container {
            max-width: 900px;
            /* Ajusta el ancho según lo desees */
            margin: 0 auto;
            padding: 20px;
        }

        /* Estilos para el mensaje de error de PHP */
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .alert-danger ul {
            margin: 0;
            padding-left: 20px;
        }

        .alert-danger li {
            list-style-type: disc;
        }

        /* Ajustes para el botón "Siguiente" */
        .admin-form .form-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        /* Para el h2 que no está dentro de admin-section */
        .main-content-container>h2 {
            color: var(--primary-color);
            margin-bottom: 25px;
            font-size: 1.8rem;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            display: inline-block;
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

    <div class="main-content-container">
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

        <div class="admin-section">
            <form class="admin-form" method="POST">
                <div class="admin-form-fields">
                    <div>
                        <label for="nombre"><span class="rojo">*</span> Nombre del curso:</label>
                        <input type="text" name="nombre" id="nombre" placeholder="Introduce el nombre del curso"
                            required>
                    </div>

                    <div class="date-pair">
                        <label for="fecha_inicio"><span class="rojo">*</span> Fechas del curso:</label>
                        <div>
                            <label for="fecha_inicio">Inicio:</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" required>
                        </div>
                        <div>
                            <label for="fecha_fin">Fin:</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" required>
                        </div>
                    </div>

                    <div class="date-pair">
                        <label for="fecha_inicio_inscripciones"><span class="rojo">*</span> Fechas de
                            inscripción:</label>
                        <div>
                            <label for="fecha_inicio_inscripciones">Inicio:</label>
                            <input type="date" name="fecha_inicio_inscripciones" id="fecha_inicio_inscripciones"
                                required>
                        </div>
                        <div>
                            <label for="fecha_fin_inscripciones">Fin:</label>
                            <input type="date" name="fecha_fin_inscripciones" id="fecha_fin_inscripciones" required>
                        </div>
                    </div>

                    <div>
                        <label for="cupos"><span class="rojo">*</span> Cupos disponibles:</label>
                        <input type="number" name="cupos" id="cupos" min="1" placeholder="Ej: 30" required>
                    </div>
                    <div>
                        <label for="horas"><span class="rojo">*</span> Horas académicas:</label>
                        <input type="number" name="horas" id="horas" min="1" placeholder="Ej: 40" required>
                    </div>
                    <div>
                        <label for="ponentes"><span class="rojo">*</span> Ponente(s):</label>
                        <input type="text" name="ponentes" id="ponentes" placeholder="Nombres de los ponentes" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit">Siguiente »</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>