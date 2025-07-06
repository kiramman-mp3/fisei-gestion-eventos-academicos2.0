<?php
require_once '../session.php';
require_once '../sql/conexion.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión.']);
    exit;
}

header('Content-Type: application/json');

// Detectar si es una petición JSON o form data
$input = file_get_contents("php://input");
if (!empty($input) && json_decode($input) !== null) {
    // Petición JSON (desde JavaScript)
    $data = json_decode($input, true);
    $evento_id = $data['evento_id'] ?? null;
    $texto_adicional = $data['texto_adicional'] ?? null;
} else {
    // Petición GET o POST normal
    $evento_id = $_GET['evento_id'] ?? $_POST['evento_id'] ?? null;
    $texto_adicional = $_POST['texto_adicional'] ?? null;
}

$usuario_id = getUserId();

// Si es una petición GET sin procesar, mostrar página de inscripción
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $evento_id && !isset($_GET['procesar'])) {
    // Obtener información del evento
    try {
        $conn = (new Conexion())->conectar();
        $stmt = $conn->prepare("SELECT e.*, t.nombre AS tipo_evento FROM eventos e LEFT JOIN tipos_evento t ON e.tipo_evento_id = t.id WHERE e.id = ?");
        $stmt->execute([$evento_id]);
        $evento = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$evento) {
            echo json_encode(['success' => false, 'message' => 'Evento no encontrado.']);
            exit;
        }
        
        // Verificar si ya está inscrito
        $stmt = $conn->prepare("SELECT COUNT(*) FROM inscripciones WHERE usuario_id = ? AND evento_id = ?");
        $stmt->execute([$usuario_id, $evento_id]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Ya estás inscrito en este curso.']);
            exit;
        }
        
        // Mostrar página de confirmación
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Confirmar Inscripción - <?= htmlspecialchars($evento['nombre_evento']) ?></title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        </head>
        <body>
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h4 class="mb-0"><i class="fas fa-check-circle me-2"></i>Confirmar Inscripción</h4>
                            </div>
                            <div class="card-body">
                                <h5><?= htmlspecialchars($evento['nombre_evento']) ?></h5>
                                <p class="text-muted"><?= htmlspecialchars($evento['tipo_evento'] ?? '') ?></p>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>¡Requisitos completados!</strong><br>
                                    Has subido todos los documentos necesarios. ¿Deseas confirmar tu inscripción a este evento?
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Fecha de inicio:</strong> <?= date('d/m/Y', strtotime($evento['fecha_inicio'])) ?></p>
                                        <p><strong>Fecha de fin:</strong> <?= date('d/m/Y', strtotime($evento['fecha_fin'])) ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Horas académicas:</strong> <?= $evento['horas'] ?></p>
                                        <p><strong>Cupos disponibles:</strong> <?= $evento['cupos'] ?></p>
                                    </div>
                                </div>
                                
                                <form method="POST" action="?evento_id=<?= $evento_id ?>&procesar=1">
                                    <div class="mb-3">
                                        <label for="motivacion" class="form-label">Motivación (opcional)</label>
                                        <textarea class="form-control" id="motivacion" name="texto_adicional" rows="3" 
                                                  placeholder="Cuéntanos por qué te interesa este curso..."></textarea>
                                    </div>
                                    
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="../ver_cursos.php" class="btn btn-secondary me-md-2">
                                            <i class="fas fa-arrow-left me-2"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check me-2"></i>Confirmar Inscripción
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
        exit;
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        exit;
    }
}

if (!$evento_id || !$usuario_id) {
    // Si no hay evento_id, mostrar lista de eventos disponibles
    if (!$evento_id && $usuario_id) {
        try {
            $conn = (new Conexion())->conectar();
            $stmt = $conn->query("
                SELECT e.*, t.nombre AS tipo_evento 
                FROM eventos e 
                LEFT JOIN tipos_evento t ON e.tipo_evento_id = t.id 
                WHERE e.fecha_fin_inscripciones >= CURDATE() 
                ORDER BY e.fecha_inicio ASC
            ");
            $eventos_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            ?>
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Seleccionar Evento - FISEI</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            </head>
            <body>
                <div class="container mt-5">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h4 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Seleccionar Evento para Inscripción</h4>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($eventos_disponibles)): ?>
                                        <div class="text-center py-4">
                                            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                            <h5 class="text-muted">No hay eventos disponibles</h5>
                                            <p class="text-muted">No hay eventos con inscripciones abiertas en este momento.</p>
                                            <a href="../ver_cursos.php" class="btn btn-primary">
                                                <i class="fas fa-arrow-left me-2"></i>Volver a Cursos
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <p class="mb-4">Selecciona el evento al que deseas inscribirte:</p>
                                        <div class="row">
                                            <?php foreach ($eventos_disponibles as $evento): ?>
                                                <div class="col-md-6 mb-3">
                                                    <div class="card h-100">
                                                        <div class="card-body">
                                                            <h5 class="card-title"><?= htmlspecialchars($evento['nombre_evento']) ?></h5>
                                                            <p class="card-text">
                                                                <small class="text-muted"><?= htmlspecialchars($evento['tipo_evento'] ?? '') ?></small><br>
                                                                <strong>Inicio:</strong> <?= date('d/m/Y', strtotime($evento['fecha_inicio'])) ?><br>
                                                                <strong>Fin:</strong> <?= date('d/m/Y', strtotime($evento['fecha_fin'])) ?><br>
                                                                <strong>Horas:</strong> <?= $evento['horas'] ?> académicas
                                                            </p>
                                                        </div>
                                                        <div class="card-footer">
                                                            <a href="?evento_id=<?= $evento['id'] ?>" class="btn btn-primary w-100">
                                                                <i class="fas fa-user-plus me-2"></i>Inscribirse
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="text-center mt-3">
                                            <a href="../ver_cursos.php" class="btn btn-secondary">
                                                <i class="fas fa-arrow-left me-2"></i>Volver a Cursos
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            </body>
            </html>
            <?php
            exit;
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            exit;
        }
    }
    
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

    // Ver si fue una solicitud HTML (formulario) o JSON (fetch/AJAX)
    $esPostConFormulario = isset($_GET['procesar']) || $_SERVER['REQUEST_METHOD'] === 'POST';

    if ($esPostConFormulario) {
        // Mostrar HTML de éxito
        echo "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Inscripción Exitosa</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
        </head>
        <body>
            <div class='container mt-5'>
                <div class='row justify-content-center'>
                    <div class='col-md-6'>
                        <div class='card'>
                            <div class='card-body text-center'>
                                <i class='fas fa-check-circle fa-4x text-success mb-3'></i>
                                <h3 class='text-success'>¡Inscripción Exitosa!</h3>
                                <p class='mb-4'>Te has inscrito correctamente al evento. Recibirás más información por correo electrónico.</p>
                                <a href='../ver_cursos.php' class='btn btn-primary'>
                                    <i class='fas fa-arrow-left me-2'></i>Volver a Cursos
                                </a>
                                <a href='mis_cursos.php' class='btn btn-success ms-2'>
                                    <i class='fas fa-graduation-cap me-2'></i>Mis Cursos
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>";
    } else {
        // Respuesta para fetch/AJAX
        echo json_encode(['success' => true, 'message' => 'Inscripción realizada con éxito.']);
    }
} catch (PDOException $e) {
    if ($e->getCode() == '45000') {
        $errorMessage = $e->getMessage();
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
        // Detectar si se esperaba HTML o JSON para el error también
        if ($esPostConFormulario) {
            echo "<!DOCTYPE html>
            <html lang='es'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Error en Inscripción</title>
                <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
                <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
            </head>
            <body>
                <div class='container mt-5'>
                    <div class='row justify-content-center'>
                        <div class='col-md-6'>
                            <div class='card'>
                                <div class='card-body text-center'>
                                    <i class='fas fa-exclamation-triangle fa-4x text-danger mb-3'></i>
                                    <h3 class='text-danger'>Error en la Inscripción</h3>
                                    <p class='mb-4'>Ocurrió un error al procesar tu inscripción: " . htmlspecialchars($e->getMessage()) . "</p>
                                    <a href='../ver_cursos.php' class='btn btn-primary'>
                                        <i class='fas fa-arrow-left me-2'></i>Volver a Cursos
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </body>
            </html>";
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Error al procesar la inscripción: ' . $e->getMessage(),
                'error_type' => 'database_error'
            ]);
        }
    }
}
