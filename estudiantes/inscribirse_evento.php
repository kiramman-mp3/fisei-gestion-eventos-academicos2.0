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

    $stmt = $conn->prepare("SELECT COUNT(*) FROM inscripciones WHERE usuario_id = ? AND evento_id = ?");
    $stmt->execute([$usuario_id, $evento_id]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Ya estás inscrito en este curso.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, tipo, campo_estudiante, descripcion FROM requisitos_evento WHERE evento_id = ?");
    $stmt->execute([$evento_id]);
    $requisitos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $reqCumplidos = [];
    if ($requisitos) {
        // Cambiado a `id` en lugar de `usuario_id`
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

    $stmt = $conn->prepare("INSERT INTO inscripciones (usuario_id, evento_id, legalizado, pago_confirmado) VALUES (?, ?, 0, 0)");
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
    echo json_encode(['success' => false, 'message' => 'Error al inscribirse: ' . $e->getMessage()]);
}
