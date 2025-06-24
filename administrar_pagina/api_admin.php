<?php
include '../sql/conexion.php';
$conn = (new Conexion())->conectar();

function subirImagen($archivo, $imgActual = null) {
    $dirServidor = realpath(__DIR__ . '/../uploads/landing/') . DIRECTORY_SEPARATOR;
    if (!is_dir($dirServidor)) mkdir($dirServidor, 0777, true);

    $nombreArchivo = uniqid() . '_' . basename($archivo['name']);
    $rutaDestinoServidor = $dirServidor . $nombreArchivo;

    if (move_uploaded_file($archivo['tmp_name'], $rutaDestinoServidor)) {
        if ($imgActual) {
            $rutaImagenAnteriorServidor = realpath(__DIR__ . '/../' . $imgActual);
            if ($rutaImagenAnteriorServidor && file_exists($rutaImagenAnteriorServidor)) {
                unlink($rutaImagenAnteriorServidor);
            }
        }
        return 'uploads/landing/' . $nombreArchivo;
    }
    return $imgActual;
}

// ELIMINAR
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("SELECT tipo, contenido FROM info_fisei WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        if (in_array($row['tipo'], ['carrusel', 'nosotros', 'autoridad', 'resena'])) {
            $json = json_decode($row['contenido'], true);
            if (isset($json['img']) && file_exists('../' . $json['img'])) {
                unlink('../' . $json['img']);
            }
        }
        $stmt = $conn->prepare("DELETE FROM info_fisei WHERE id = ?");
        $stmt->execute([$id]);
    }

    header("Location: admin.php");
    exit;
}

// AGREGAR O ACTUALIZAR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? '';
    $id = $_POST['id'] ?? null;
    $imagen_actual = $_POST['imagen_actual'] ?? null;

    // ========================
    // MISIÓN y VISIÓN (texto plano)
    // ========================
    if ($tipo === 'mision' || $tipo === 'vision') {
        $texto = isset($_POST['texto']) ? trim($_POST['texto']) : '';

        if ($id) {
            $stmt = $conn->prepare("UPDATE info_fisei SET contenido = ? WHERE id = ?");
            $stmt->execute([$texto, $id]);
        } else {
            $stmt = $conn->prepare("INSERT INTO info_fisei (tipo, contenido) VALUES (?, ?)");
            $stmt->execute([$tipo, $texto]);
        }

        header("Location: admin.php");
        exit;
    }

    // ========================
    // OTROS tipos con contenido JSON e imagen
    // ========================
    $contenido = [];

    if ($tipo === 'carrusel' || $tipo === 'nosotros') {
        $contenido['titulo'] = $_POST['titulo'] ?? '';
        $contenido['descripcion'] = $_POST['descripcion'] ?? '';
    } elseif ($tipo === 'autoridad') {
        $contenido['nombre'] = $_POST['nombre'] ?? '';
        $contenido['cargo'] = $_POST['cargo'] ?? '';
    } elseif ($tipo === 'resena') {
        $contenido['autor'] = $_POST['autor'] ?? '';
        $contenido['rol'] = $_POST['rol'] ?? '';
        $contenido['texto'] = $_POST['texto'] ?? '';
    }

    // Imagen (si aplica)
    if (isset($_FILES['nueva_img']) && $_FILES['nueva_img']['error'] === 0) {
        $contenido['img'] = subirImagen($_FILES['nueva_img'], $imagen_actual);
    } else {
        $contenido['img'] = $imagen_actual;
    }

    $jsonContenido = json_encode($contenido, JSON_UNESCAPED_UNICODE);

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
