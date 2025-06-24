<?php
include '../sql/conexion.php';
$conn = (new Conexion())->conectar();

function subirImagen($archivo, $imgActual = null) {
    $dirServidor = realpath(__DIR__ . '/../uploads/landing/') . DIRECTORY_SEPARATOR;
    if (!is_dir($dirServidor)) mkdir($dirServidor, 0777, true);

    $nombreArchivo = uniqid() . '_' . basename($archivo['name']);
    $rutaDestinoServidor = $dirServidor . $nombreArchivo;

    if (move_uploaded_file($archivo['tmp_name'], $rutaDestinoServidor)) {
        // Eliminar imagen anterior si existe (ruta guardada en DB es relativa)
        if ($imgActual) {
            $rutaImagenAnteriorServidor = realpath(__DIR__ . '/../' . $imgActual);
            if ($rutaImagenAnteriorServidor && file_exists($rutaImagenAnteriorServidor)) {
                unlink($rutaImagenAnteriorServidor);
            }
        }
        // Devolver ruta relativa para usar en URLs
        return 'uploads/landing/' . $nombreArchivo;
    }
    return $imgActual; // devuelve la ruta anterior si fallo la subida
}

// ELIMINAR
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("SELECT contenido FROM info_fisei WHERE id = ?");
    $stmt->execute([$id]);
    $contenido = $stmt->fetchColumn();
    if ($contenido) {
        $json = json_decode($contenido, true);
        if (isset($json['img']) && file_exists($json['img'])) {
            unlink($json['img']);
        }
    }
    $stmt = $conn->prepare("DELETE FROM info_fisei WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin.php");
    exit;
}

// AGREGAR O ACTUALIZAR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? '';
    $id = $_POST['id'] ?? null;
    $imagen_actual = $_POST['imagen_actual'] ?? null;

    // Preparar contenido JSON según tipo
    $contenido = [];

    if ($tipo === 'carrusel' || $tipo === 'nosotros') {
        $contenido['titulo'] = $_POST['titulo'];
        $contenido['descripcion'] = $_POST['descripcion'];
    } elseif ($tipo === 'autoridad') {
        $contenido['nombre'] = $_POST['nombre'];
        $contenido['cargo'] = $_POST['cargo'];
    } elseif ($tipo === 'resena') {
        $contenido['autor'] = $_POST['autor'];
        $contenido['rol'] = $_POST['rol'];
        $contenido['texto'] = $_POST['texto'];
    }

    // Manejar imagen
    if (isset($_FILES['nueva_img']) && $_FILES['nueva_img']['error'] === 0) {
        $contenido['img'] = subirImagen($_FILES['nueva_img'], $imagen_actual);
    } else {
        $contenido['img'] = $imagen_actual;
    }

    $jsonContenido = json_encode($contenido, JSON_UNESCAPED_UNICODE);

    // Actualizar si viene con ID
    if ($id) {
        $stmt = $conn->prepare("UPDATE info_fisei SET contenido = ? WHERE id = ?");
        $stmt->execute([$jsonContenido, $id]);
    } else {
        $stmt = $conn->prepare("INSERT INTO info_fisei (tipo, contenido) VALUES (?, ?)");
        $stmt->execute([$tipo, $jsonContenido]);
    }

    header("Location: admin.php");
    exit;
}
