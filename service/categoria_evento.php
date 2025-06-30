<?php
require_once '../session.php';
require_once '../sql/conexion.php';

$cris = new Conexion();
$conn = $cris->conectar(); // Asume que esto devuelve una instancia de PDO

// --- Mejora 1: Manejo de errores de PDO y Encabezado JSON ---
// Establecer el modo de error de PDO para lanzar excepciones, facilitando la depuración
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Asegurarse de que el navegador sepa que la respuesta es JSON
header('Content-Type: application/json');
// --- Fin Mejora 1 ---

$action = $_GET['action'] ?? '';

// --- Mejora 2: Inicializar la variable $response ---
// Es crucial inicializar $response para asegurar que siempre tenga una estructura definida,
// especialmente si ninguna de las condiciones de acción se cumple o hay un error.
$response = ['success' => false, 'mensaje' => 'Operación no reconocida o fallida.'];
// --- Fin Mejora 2 ---

try {
    if ($action === 'listar') {
        $stmt = $conn->query("SELECT id, nombre FROM categorias_evento"); // Seleccionar solo las columnas necesarias
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit; // Terminar la ejecución después de listar, ya que ya se envió la respuesta
    }

    if ($action === 'crear') {
        // --- Mejora 3: Validaciones más estrictas para 'crear' ---
        if (!isset($_POST['nombre']) || empty(trim($_POST['nombre']))) {
            $response['mensaje'] = 'El nombre de la categoría no puede estar vacío.';
        } else {
            $nombre = trim($_POST['nombre']);
            $stmt = $conn->prepare("INSERT INTO categorias_evento (nombre) VALUES (?)");
            if ($stmt->execute([$nombre])) {
                $response['success'] = true;
                $response['mensaje'] = 'Categoría creada exitosamente.';
            } else {
                // Esto es poco probable si PDO::ERRMODE_EXCEPTION está activado, pero como respaldo.
                $response['mensaje'] = 'Error al insertar la categoría en la base de datos.';
            }
        }
        // --- Fin Mejora 3 ---
    }

    if ($action === 'editar') {
        // La lógica de validación ya estaba bien.
        if (empty($_POST['id']) || empty(trim($_POST['nombre']))) { // Usar trim para nombre
            $response['mensaje'] = 'ID y nombre de categoría son obligatorios para editar.';
        } else {
            $id = $_POST['id'];
            $nombre = trim($_POST['nombre']);
            $stmt = $conn->prepare("UPDATE categorias_evento SET nombre=? WHERE id=?");
            if ($stmt->execute([$nombre, $id])) {
                $response['success'] = true;
                $response['mensaje'] = 'Categoría actualizada correctamente.';
            } else {
                // Esto es poco probable si PDO::ERRMODE_EXCEPTION está activado, pero como respaldo.
                $response['mensaje'] = 'Error al actualizar la categoría en la base de datos.';
            }
        }
    }

    // --- Mejora 4: Agregar lógica para 'eliminar' (opcional, pero buena práctica) ---
    // Si tienes un botón de eliminar, tu frontend lo buscará.
    if ($action === 'eliminar') {
        if (empty($_GET['id'])) {
            $response['mensaje'] = 'ID de categoría no proporcionado para eliminar.';
        } else {
            $id = $_GET['id'];
            $stmt = $conn->prepare("DELETE FROM categorias_evento WHERE id=?");
            if ($stmt->execute([$id])) {
                $response['success'] = true;
                $response['mensaje'] = 'Categoría eliminada correctamente.';
            } else {
                $response['mensaje'] = 'Error al eliminar la categoría.';
            }
        }
    }
    // --- Fin Mejora 4 ---

} catch (PDOException $e) {
    // --- Mejora 5: Manejo de Excepciones para errores de BD ---
    // Captura errores específicos de la base de datos
    $response['success'] = false; // Asegura que 'success' sea false en caso de excepción
    $response['mensaje'] = 'Error en la base de datos: ' . $e->getMessage();
    // En un entorno de producción, NO mostrar $e->getMessage() al usuario final.
    // En su lugar, registrar el error y mostrar un mensaje genérico.
    error_log("Error PDO en categoria_evento.php: " . $e->getMessage());
    // --- Fin Mejora 5 ---
} catch (Exception $e) {
    // --- Mejora 6: Manejo de Excepciones generales ---
    // Captura cualquier otra excepción no controlada
    $response['success'] = false;
    $response['mensaje'] = 'Ocurrió un error inesperado: ' . $e->getMessage();
    error_log("Error general en categoria_evento.php: " . $e->getMessage());
    // --- Fin Mejora 6 ---
}

// --- Mejora 7: Salida consistente de JSON ---
// Asegurarse de que solo se imprima un JSON al final de la ejecución
echo json_encode($response);
// --- Fin Mejora 7 ---
?>