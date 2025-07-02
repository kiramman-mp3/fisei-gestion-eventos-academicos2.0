<?php
require_once '../session.php';
require_once '../sql/conexion.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión.']);
    exit;
}

header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);
$evento_id = $data['evento_id'] ?? null;
$usuario_id = getUserId();
$texto_adicional = $data['texto_adicional'] ?? null;

if (!$evento_id || !$usuario_id) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit;
}

try {
    $conn = (new Conexion())->conectar();

    // Verificar si ya está inscrito
    $stmt = $conn->prepare("SELECT COUNT(*) FROM inscripciones WHERE usuario_id = ? AND evento_id = ?");
    $stmt->execute([$usuario_id, $evento_id]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Ya estás inscrito en este curso.']);
        exit;
    }

    // Verificar requisitos del evento
    $stmt = $conn->prepare("SELECT id, tipo, campo_estudiante, descripcion FROM requisitos_evento WHERE evento_id = ?");
    $stmt->execute([$evento_id]);
    $requisitos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $reqCumplidos = [];
    if ($requisitos) {
        $stmt = $conn->prepare("SELECT cedula_path, matricula_path, papeleta_path FROM estudiantes WHERE id = ?");
        $stmt->execute([$usuario_id]);
        $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

        foreach ($requisitos as $req) {
            if ($req['tipo'] === 'archivo') {
                $campo = $req['campo_estudiante'];
                if (!isset($estudiante[$campo]) || $estudiante[$campo] === null) {
                    echo json_encode([
                        'success' => false,
                        'message' => "Falta el documento requerido: " . $req['descripcion']
                    ]);
                    exit;
                }
                $reqCumplidos[] = ['id' => $req['id'], 'texto' => null];
            } elseif ($req['tipo'] === 'texto') {
                if (!$texto_adicional || strlen(trim($texto_adicional)) < 10) {
                    echo json_encode([
                        'success' => false,
                        'message' => "Debes completar el siguiente requisito textual: " . $req['descripcion']
                    ]);
                    exit;
                }
                $reqCumplidos[] = ['id' => $req['id'], 'texto' => trim($texto_adicional)];
            }
        }
    }

    // Intentar insertar la inscripción - el trigger verificará automáticamente los cupos
    $stmt = $conn->prepare("INSERT INTO inscripciones (usuario_id, evento_id, legalizado, pago_confirmado, estado) VALUES (?, ?, 0, 0, 'activo')");
    $stmt->execute([$usuario_id, $evento_id]);
    $inscripcion_id = $conn->lastInsertId();

    // Insertar requisitos cumplidos
    if (!empty($reqCumplidos)) {
        $stmtReq = $conn->prepare("INSERT INTO requisitos_inscripcion (inscripcion_id, requisito_id, archivo) VALUES (?, ?, ?)");
        foreach ($reqCumplidos as $r) {
            $stmtReq->execute([$inscripcion_id, $r['id'], $r['texto']]);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Inscripción realizada con éxito.']);

} catch (PDOException $e) {
    // Verificar si el error es del trigger de cupos
    if ($e->getCode() == '45000') {
        // Error personalizado del trigger
        $errorMessage = $e->getMessage();
        
        // Personalizar mensaje si es el error básico del trigger
        if (strpos($errorMessage, 'No hay cupos disponibles') !== false) {
            echo json_encode([
                'success' => false, 
                'message' => '🚫 Este curso ha alcanzado el límite máximo de cupos. No hay lugares disponibles en este momento.',
                'error_type' => 'cupos_llenos'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => $errorMessage,
                'error_type' => 'trigger_error'
            ]);
        }
    } else {
        // Otros errores de base de datos
        echo json_encode([
            'success' => false, 
            'message' => 'Error al procesar la inscripción: ' . $e->getMessage(),
            'error_type' => 'database_error'
        ]);
    }
}
