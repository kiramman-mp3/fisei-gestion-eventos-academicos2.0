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
$evento_seleccionado = null;
$inscripciones = [];

// Obtener el evento seleccionado
$evento_id = $_GET['evento_id'] ?? $_POST['evento_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_notas'])) {
    // Procesar actualización de notas
    try {
        $notas = $_POST['notas'] ?? [];
        $asistencias = $_POST['asistencias'] ?? [];
        $estados = $_POST['estados'] ?? [];
        
        foreach ($notas as $inscripcion_id => $nota) {
            $asistencia = $asistencias[$inscripcion_id] ?? 0;
            $estado = $estados[$inscripcion_id] ?? 'pendiente';
            
            $stmt = $conn->prepare("UPDATE inscripciones SET nota = ?, asistencia = ?, estado = ? WHERE id = ?");
            $stmt->execute([$nota, $asistencia, $estado, $inscripcion_id]);
        }
        
        $mensaje = "Notas actualizadas correctamente.";
        $tipo_mensaje = "success";
    } catch (PDOException $e) {
        $mensaje = "Error al actualizar notas: " . $e->getMessage();
        $tipo_mensaje = "danger";
    }
}

// Obtener lista de eventos
$stmt = $conn->query("
    SELECT e.id, e.nombre_evento, e.fecha_inicio, e.fecha_fin, 
           COUNT(i.id) as total_inscritos
    FROM eventos e 
    LEFT JOIN inscripciones i ON e.id = i.evento_id 
    GROUP BY e.id 
    ORDER BY e.fecha_inicio DESC
");
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si hay evento seleccionado, obtener sus inscripciones
if ($evento_id) {
    // Obtener información del evento
    $stmt = $conn->prepare("
        SELECT e.*, c.nombre as categoria_nombre, c.requiere_nota, c.requiere_asistencia
        FROM eventos e 
        LEFT JOIN categorias_evento c ON e.categoria_id = c.id 
        WHERE e.id = ?
    ");
    $stmt->execute([$evento_id]);
    $evento_seleccionado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Obtener inscripciones del evento
    $stmt = $conn->prepare("
        SELECT i.*, est.nombre, est.apellido, est.correo, est.cedula
        FROM inscripciones i
        JOIN estudiantes est ON i.usuario_id = est.id
        WHERE i.evento_id = ?
        ORDER BY est.apellido, est.nombre
    ");
    $stmt->execute([$evento_id]);
    $inscripciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Notas - Admin FISEI</title>
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
                            Gestión de Notas y Calificaciones
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
            
            <!-- Selección de evento -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar me-2"></i>Seleccionar Evento
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-8">
                                    <select class="form-select" name="evento_id" onchange="this.form.submit()">
                                        <option value="">Seleccionar evento...</option>
                                        <?php foreach ($eventos as $evento): ?>
                                            <option value="<?= $evento['id'] ?>" <?= $evento['id'] == $evento_id ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($evento['nombre_evento']) ?> 
                                                (<?= date('d/m/Y', strtotime($evento['fecha_inicio'])) ?>) 
                                                - <?= $evento['total_inscritos'] ?> inscritos
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <?php if ($evento_id): ?>
                                        <a href="lista_eventos.php" class="btn btn-secondary">
                                            <i class="fas fa-list me-2"></i>Ver todos los eventos
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if ($evento_seleccionado && !empty($inscripciones)): ?>
                <!-- Información del evento -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5><?= htmlspecialchars($evento_seleccionado['nombre_evento']) ?></h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Categoría:</strong> <?= htmlspecialchars($evento_seleccionado['categoria_nombre'] ?? 'N/A') ?></p>
                                        <p class="mb-1"><strong>Fechas:</strong> 
                                            <?= date('d/m/Y', strtotime($evento_seleccionado['fecha_inicio'])) ?> - 
                                            <?= date('d/m/Y', strtotime($evento_seleccionado['fecha_fin'])) ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Total inscritos:</strong> <?= count($inscripciones) ?></p>
                                        <p class="mb-1"><strong>Horas académicas:</strong> <?= $evento_seleccionado['horas'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tabla de calificaciones -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-star me-2"></i>Calificaciones de Estudiantes
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="evento_id" value="<?= $evento_id ?>">
                                    <input type="hidden" name="actualizar_notas" value="1">
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Estudiante</th>
                                                    <th>Cédula</th>
                                                    <th>Email</th>
                                                    <th>Nota</th>
                                                    <th>Asistencia (%)</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($inscripciones as $inscripcion): ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?= htmlspecialchars($inscripcion['nombre'] . ' ' . $inscripcion['apellido']) ?></strong>
                                                        </td>
                                                        <td><?= htmlspecialchars($inscripcion['cedula']) ?></td>
                                                        <td><?= htmlspecialchars($inscripcion['correo']) ?></td>
                                                        <td>
                                                            <input type="number" 
                                                                   class="form-control form-control-sm" 
                                                                   name="notas[<?= $inscripcion['id'] ?>]" 
                                                                   value="<?= htmlspecialchars($inscripcion['nota'] ?? '') ?>"
                                                                   min="0" max="100" step="0.1"
                                                                   placeholder="0-100">
                                                        </td>
                                                        <td>
                                                            <input type="number" 
                                                                   class="form-control form-control-sm" 
                                                                   name="asistencias[<?= $inscripcion['id'] ?>]" 
                                                                   value="<?= htmlspecialchars($inscripcion['asistencia'] ?? '') ?>"
                                                                   min="0" max="100" step="1"
                                                                   placeholder="0-100">
                                                        </td>
                                                        <td>
                                                            <select class="form-select form-select-sm" name="estados[<?= $inscripcion['id'] ?>]">
                                                                <option value="pendiente" <?= $inscripcion['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                                                <option value="aprobado" <?= $inscripcion['estado'] === 'aprobado' ? 'selected' : '' ?>>Aprobado</option>
                                                                <option value="reprobado" <?= $inscripcion['estado'] === 'reprobado' ? 'selected' : '' ?>>Reprobado</option>
                                                                <option value="retirado" <?= $inscripcion['estado'] === 'retirado' ? 'selected' : '' ?>>Retirado</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="text-end mt-3">
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-save me-2"></i>Guardar Calificaciones
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
            <?php elseif ($evento_seleccionado && empty($inscripciones)): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-2x mb-3"></i>
                            <h4>No hay inscripciones</h4>
                            <p>El evento seleccionado no tiene estudiantes inscritos.</p>
                        </div>
                    </div>
                </div>
                
            <?php elseif (!$evento_id): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-primary text-center">
                            <i class="fas fa-arrow-up fa-2x mb-3"></i>
                            <h4>Selecciona un evento</h4>
                            <p>Para gestionar las notas, primero selecciona un evento de la lista desplegable.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-calcular estado basado en nota
        document.querySelectorAll('input[name^="notas"]').forEach(input => {
            input.addEventListener('change', function() {
                const nota = parseFloat(this.value);
                const inscripcionId = this.name.match(/\[(\d+)\]/)[1];
                const estadoSelect = document.querySelector(`select[name="estados[${inscripcionId}]"]`);
                
                if (!isNaN(nota)) {
                    if (nota >= 70) {
                        estadoSelect.value = 'aprobado';
                    } else if (nota < 70 && nota >= 0) {
                        estadoSelect.value = 'reprobado';
                    }
                }
            });
        });
    </script>
</body>
</html>
