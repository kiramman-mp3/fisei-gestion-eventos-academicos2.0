<?php
require_once '../session.php';
require_once '../sql/conexion.php';

// Verificar que el usuario sea administrador
if (!isLoggedIn() || getUserRole() !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

$cris = new Conexion();
$conn = $cris->conectar();

$mensaje = '';
$tipo_mensaje = '';

// Manejar la creación/edición de requisitos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    try {
        switch ($accion) {
            case 'crear_requisito':
                $evento_id = $_POST['evento_id'] ?? '';
                $descripcion = $_POST['descripcion'] ?? '';
                $tipo = $_POST['tipo'] ?? '';
                $campo_estudiante = $_POST['campo_estudiante'] ?? '';
                $es_obligatorio = isset($_POST['es_obligatorio']) ? 1 : 0;
                
                if ($evento_id && $descripcion && $tipo) {
                    $stmt = $conn->prepare("INSERT INTO requisitos_evento (evento_id, descripcion, tipo, campo_estudiante, es_obligatorio) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$evento_id, $descripcion, $tipo, $campo_estudiante, $es_obligatorio]);
                    $mensaje = "Requisito creado correctamente.";
                    $tipo_mensaje = "success";
                } else {
                    $mensaje = "Por favor, complete todos los campos obligatorios.";
                    $tipo_mensaje = "danger";
                }
                break;
                
            case 'eliminar_requisito':
                $requisito_id = $_POST['requisito_id'] ?? '';
                if ($requisito_id) {
                    $stmt = $conn->prepare("DELETE FROM requisitos_evento WHERE id = ?");
                    $stmt->execute([$requisito_id]);
                    $mensaje = "Requisito eliminado correctamente.";
                    $tipo_mensaje = "success";
                }
                break;
        }
    } catch (PDOException $e) {
        $mensaje = "Error: " . $e->getMessage();
        $tipo_mensaje = "danger";
    }
}

// Obtener todos los eventos
$stmt = $conn->query("SELECT e.id, e.nombre_evento, e.fecha_inicio, e.fecha_fin FROM eventos e ORDER BY e.fecha_inicio DESC");
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener requisitos por evento
$requisitos_por_evento = [];
if (!empty($eventos)) {
    $stmt = $conn->query("
        SELECT r.*, e.nombre_evento 
        FROM requisitos_evento r 
        JOIN eventos e ON r.evento_id = e.id 
        ORDER BY e.nombre_evento, r.id
    ");
    while ($req = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $requisitos_por_evento[$req['evento_id']][] = $req;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Requisitos - Admin FISEI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/panel-estilos.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Header -->
            <div class="col-12">
                <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                    <div class="container-fluid">
                        <a class="navbar-brand" href="panel_admin.php">
                            <i class="fas fa-arrow-left me-2"></i>Panel Admin
                        </a>
                        <span class="navbar-text">
                            Gestión de Requisitos
                        </span>
                    </div>
                </nav>
            </div>
        </div>
        
        <div class="container-fluid mt-4">
            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($mensaje) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <!-- Crear nuevo requisito -->
                <div class="col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-plus me-2"></i>Crear Nuevo Requisito
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="accion" value="crear_requisito">
                                
                                <div class="mb-3">
                                    <label for="evento_id" class="form-label">Evento</label>
                                    <select class="form-select" id="evento_id" name="evento_id" required>
                                        <option value="">Seleccionar evento...</option>
                                        <?php foreach ($eventos as $evento): ?>
                                            <option value="<?= $evento['id'] ?>">
                                                <?= htmlspecialchars($evento['nombre_evento']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción del Requisito</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required
                                              placeholder="Ej: Cédula de identidad, Certificado de matrícula, etc."></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="tipo" class="form-label">Tipo de Requisito</label>
                                    <select class="form-select" id="tipo" name="tipo" required onchange="toggleCampoEstudiante()">
                                        <option value="">Seleccionar tipo...</option>
                                        <option value="archivo">Archivo (Documento)</option>
                                        <option value="texto">Texto (Motivación/Comentario)</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3" id="campo_estudiante_div" style="display: none;">
                                    <label for="campo_estudiante" class="form-label">Campo en Base de Datos</label>
                                    <select class="form-select" id="campo_estudiante" name="campo_estudiante">
                                        <option value="">Seleccionar campo...</option>
                                        <option value="cedula_path">Cédula</option>
                                        <option value="matricula_path">Matrícula</option>
                                        <option value="papeleta_path">Papeleta de Votación</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="es_obligatorio" name="es_obligatorio" checked>
                                        <label class="form-check-label" for="es_obligatorio">
                                            Requisito obligatorio
                                        </label>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-plus me-2"></i>Crear Requisito
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Lista de requisitos existentes -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>Requisitos por Evento
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($eventos)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay eventos disponibles.</p>
                                    <a href="crear_curso_p1.php" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Crear Primer Evento
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="accordion" id="accordionRequisitos">
                                    <?php foreach ($eventos as $evento): ?>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading<?= $evento['id'] ?>">
                                                <button class="accordion-button collapsed" type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#collapse<?= $evento['id'] ?>" 
                                                        aria-expanded="false" 
                                                        aria-controls="collapse<?= $evento['id'] ?>">
                                                    <div>
                                                        <strong><?= htmlspecialchars($evento['nombre_evento']) ?></strong>
                                                        <small class="d-block text-muted">
                                                            <?= date('d/m/Y', strtotime($evento['fecha_inicio'])) ?> - 
                                                            <?= date('d/m/Y', strtotime($evento['fecha_fin'])) ?>
                                                        </small>
                                                        <?php if (isset($requisitos_por_evento[$evento['id']])): ?>
                                                            <span class="badge bg-primary ms-2">
                                                                <?= count($requisitos_por_evento[$evento['id']]) ?> requisitos
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse<?= $evento['id'] ?>" 
                                                 class="accordion-collapse collapse" 
                                                 aria-labelledby="heading<?= $evento['id'] ?>" 
                                                 data-bs-parent="#accordionRequisitos">
                                                <div class="accordion-body">
                                                    <?php if (isset($requisitos_por_evento[$evento['id']])): ?>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Descripción</th>
                                                                        <th>Tipo</th>
                                                                        <th>Obligatorio</th>
                                                                        <th>Acciones</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($requisitos_por_evento[$evento['id']] as $req): ?>
                                                                        <tr>
                                                                            <td><?= htmlspecialchars($req['descripcion']) ?></td>
                                                                            <td>
                                                                                <span class="badge bg-<?= $req['tipo'] === 'archivo' ? 'primary' : 'success' ?>">
                                                                                    <?= ucfirst($req['tipo']) ?>
                                                                                </span>
                                                                            </td>
                                                                            <td>
                                                                                <?php if ($req['es_obligatorio']): ?>
                                                                                    <span class="badge bg-warning">Obligatorio</span>
                                                                                <?php else: ?>
                                                                                    <span class="badge bg-secondary">Opcional</span>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                            <td>
                                                                                <form method="POST" style="display: inline;">
                                                                                    <input type="hidden" name="accion" value="eliminar_requisito">
                                                                                    <input type="hidden" name="requisito_id" value="<?= $req['id'] ?>">
                                                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                                            onclick="return confirm('¿Eliminar este requisito?')">
                                                                                        <i class="fas fa-trash"></i>
                                                                                    </button>
                                                                                </form>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="text-center py-3">
                                                            <i class="fas fa-clipboard-list fa-2x text-muted mb-2"></i>
                                                            <p class="text-muted mb-0">No hay requisitos configurados para este evento.</p>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleCampoEstudiante() {
            const tipoSelect = document.getElementById('tipo');
            const campoDiv = document.getElementById('campo_estudiante_div');
            const campoSelect = document.getElementById('campo_estudiante');
            
            if (tipoSelect.value === 'archivo') {
                campoDiv.style.display = 'block';
                campoSelect.required = true;
            } else {
                campoDiv.style.display = 'none';
                campoSelect.required = false;
                campoSelect.value = '';
            }
        }
    </script>
</body>
</html>
