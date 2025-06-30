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
        if ($modalidad)
            $_SESSION['nuevo_curso']['modalidad'] = $modalidad;
        if ($descripcion)
            $_SESSION['nuevo_curso']['descripcion_adicional'] = $descripcion;

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

        /* Para el h2 que no está dentro de admin-section */
        .main-content-container>h2 {
            color: var(--primary-color);
            margin-bottom: 25px;
            font-size: 1.8rem;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            display: inline-block;
        }

        /* Estilos para las acciones del formulario (botones volver/siguiente) */
        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .form-actions .back-button {
            background-color: #6c757d;
            /* Color secundario para el botón de volver */
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        .form-actions .back-button:hover {
            background-color: #5a6268;
            transform: translateY(-1px);
        }

        .admin-form textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--input-border-color);
            border-radius: 5px;
            font-size: 1rem;
            color: var(--text-color);
            background-color: #fff;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, .075);
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            resize: vertical;
            /* Permite redimensionar verticalmente */
        }

        .admin-form textarea:focus {
            border-color: var(--input-focus-border-color);
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(176, 42, 55, .25);
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

        <div class="admin-section">
            <form class="admin-form" method="POST">
                <div class="admin-form-fields">
                    <div>
                        <label for="tipo_evento_id"><span class="rojo">*</span> Tipo de evento:</label>
                        <select name="tipo_evento_id" id="tipo_evento_id" required>
                            <option value="">Seleccione un tipo</option>
                            <?php foreach ($tiposEvento as $tipo): ?>
                                <option value="<?= $tipo['id'] ?>"><?= htmlspecialchars($tipo['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="categoria_id"><span class="rojo">*</span> Categoría:</label>
                        <select name="categoria_id" id="categoria_id" required>
                            <option value="">Seleccione una categoría</option>
                            <?php foreach ($categoriasEvento as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="modalidad">Modalidad (opcional):</label>
                        <select name="modalidad" id="modalidad">
                            <option value="">Seleccione</option>
                            <option value="presencial">Presencial</option>
                            <option value="virtual">Virtual</option>
                            <option value="híbrido">Híbrido</option>
                        </select>
                    </div>

                    <div>
                        <label for="descripcion">Descripción corta (opcional):</label>
                        <textarea name="descripcion" id="descripcion" rows="3"
                            placeholder="Añade una breve descripción"></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="crear_curso_p1.php" class="back-button">&laquo; Volver</a>
                    <button type="submit">Siguiente &raquo;</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>