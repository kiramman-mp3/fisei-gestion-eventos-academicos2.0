<?php
include '../sql/conexion.php';
$conn = (new Conexion())->conectar();

function subirImagen($archivo, $tipoImagen, $imgActual = null) {
    if ($tipoImagen === 'logo') {
        $dirServidor = realpath(__DIR__ . '/../uploads/') . DIRECTORY_SEPARATOR;
        $nombreArchivo = 'logo.png';
        $rutaDestinoServidor = $dirServidor . $nombreArchivo;

        // Validar que sea PNG
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        if (strtolower($extension) !== 'png') {
            return false; // Indicar error o no procesar si no es PNG
        }

        // Eliminar logo anterior si existe
        if (file_exists($rutaDestinoServidor)) {
            unlink($rutaDestinoServidor);
        }

        if (move_uploaded_file($archivo['tmp_name'], $rutaDestinoServidor)) {
            return 'uploads/logo.png';
        }
    } else {
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
    // LOGO
    // ========================
    if ($tipo === 'logo') {
        if (isset($_FILES['nueva_img']) && $_FILES['nueva_img']['error'] === 0) {
            $ruta_nueva_logo = subirImagen($_FILES['nueva_img'], 'logo');
            if ($ruta_nueva_logo) {
                // No necesitamos guardar en la base de datos para el logo ya que es un archivo fijo
                // y su ruta es estática. Solo lo subimos y reemplazamos.
                header("Location: admin.php");
                exit;
            } else {
                // Manejar error si no es PNG
                // Puedes redirigir con un mensaje de error o mostrarlo en la misma página
                header("Location: admin.php?error=not_png");
                exit;
            }
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

    // Imagen (si aplica y no es logo)
    if (isset($_FILES['nueva_img']) && $_FILES['nueva_img']['error'] === 0) {
        $contenido['img'] = subirImagen($_FILES['nueva_img'], $tipo, $imagen_actual);
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

// Este bloque de código está duplicado en tu archivo original y no parece ser parte
// de las operaciones de admin.php. Lo mantengo sin cambios, asumiendo que tiene otro propósito.
// Si no lo necesitas, puedes considerarlo para eliminarlo.
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->query("SELECT tipo, contenido FROM info_fisei");
    $datos = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $contenido = $row['contenido'];
        if (in_array($row['tipo'], ['autoridad', 'resena', 'carrusel', 'nosotros'])) {
            $contenido = json_decode($contenido, true);
        }
        $datos[$row['tipo']][] = $contenido;
    }
    header('Content-Type: application/json');
    echo json_encode($datos);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? '';
    $contenido = '';

    if (!$tipo) {
        http_response_code(400);
        echo "Tipo no especificado.";
        exit;
    }

    try {
        $conn->beginTransaction();

        // Para tipos estructurados con campos individuales
        if (in_array($tipo, ['carrusel', 'nosotros'])) {
            $img = $_POST['img'] ?? '';
            $titulo = $_POST['titulo'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';

            if (!$img || !$titulo || !$descripcion) {
                http_response_code(400);
                echo "Todos los campos de $tipo son obligatorios.";
                exit;
            }

            $contenido = json_encode([
                "img" => $img,
                "titulo" => $titulo,
                "descripcion" => $descripcion
            ]);
        } else {
            $contenido = $_POST['contenido'] ?? '';
            if (!$contenido) {
                http_response_code(400);
                echo "Contenido no puede estar vacío.";
                exit;
            }

            // Tipos únicos (sobrescriben)
            if (!in_array($tipo, ['autoridad', 'resena', 'evento', 'imagen', 'carrusel', 'nosotros'])) {
                $conn->prepare("DELETE FROM info_fisei WHERE tipo = ?")->execute([$tipo]);
            }
        }

        $stmt = $conn->prepare("INSERT INTO info_fisei (tipo, contenido) VALUES (?, ?)");
        $stmt->execute([$tipo, $contenido]);
        $conn->commit();
        echo "OK";
    } catch (PDOException $e) {
        $conn->rollBack();
        http_response_code(500);
        echo "Error: " . $e->getMessage();
    }
}