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

// Manejar actualización de configuraciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    try {
        switch ($accion) {
            case 'actualizar_info_general':
                $nombre_institucion = $_POST['nombre_institucion'] ?? '';
                $email_contacto = $_POST['email_contacto'] ?? '';
                $telefono_contacto = $_POST['telefono_contacto'] ?? '';
                
                // Actualizar o insertar configuración general
                $stmt = $conn->prepare("
                    INSERT INTO configuracion (clave, valor) VALUES 
                    ('nombre_institucion', :nombre),
                    ('email_contacto', :email),
                    ('telefono_contacto', :telefono)
                    ON DUPLICATE KEY UPDATE 
                    valor = VALUES(valor)
                ");
                $stmt->execute([
                    ':nombre' => $nombre_institucion,
                    ':email' => $email_contacto,
                    ':telefono' => $telefono_contacto
                ]);
                
                $mensaje = "Información general actualizada correctamente.";
                $tipo_mensaje = "success";
                break;
                
            case 'actualizar_limites':
                $max_inscripciones = (int)($_POST['max_inscripciones'] ?? 0);
                $dias_limite_cancelacion = (int)($_POST['dias_limite_cancelacion'] ?? 0);
                
                $stmt = $conn->prepare("
                    INSERT INTO configuracion (clave, valor) VALUES 
                    ('max_inscripciones_estudiante', :max_insc),
                    ('dias_limite_cancelacion', :dias_limit)
                    ON DUPLICATE KEY UPDATE 
                    valor = VALUES(valor)
                ");
                $stmt->execute([
                    ':max_insc' => $max_inscripciones,
                    ':dias_limit' => $dias_limite_cancelacion
                ]);
                
                $mensaje = "Límites del sistema actualizados correctamente.";
                $tipo_mensaje = "success";
                break;
        }
    } catch (PDOException $e) {
        $mensaje = "Error: " . $e->getMessage();
        $tipo_mensaje = "danger";
    }
}

// Obtener configuraciones actuales
$configuraciones = [];
try {
    $stmt = $conn->query("SELECT clave, valor FROM configuracion");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $configuraciones[$row['clave']] = $row['valor'];
    }
} catch (PDOException $e) {
    // Si no existe la tabla configuracion, la creamos
    try {
        $conn->exec("
            CREATE TABLE IF NOT EXISTS configuracion (
                id INT AUTO_INCREMENT PRIMARY KEY,
                clave VARCHAR(100) NOT NULL UNIQUE,
                valor TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
    } catch (PDOException $e2) {
        $mensaje = "Error al crear tabla de configuración: " . $e2->getMessage();
        $tipo_mensaje = "danger";
    }
}

// Obtener estadísticas del sistema
$estadisticas = [];
try {
    $queries = [
        'total_eventos_activos' => "SELECT COUNT(*) as count FROM eventos WHERE fecha_fin >= CURDATE()",
        'total_inscripciones_mes' => "SELECT COUNT(*) as count FROM inscripciones",
        'espacio_uploads' => "SELECT ROUND(SUM(LENGTH(contenido))/1024/1024, 2) as size_mb FROM info_fisei WHERE contenido IS NOT NULL"
    ];
    
    foreach ($queries as $key => $query) {
        $stmt = $conn->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $estadisticas[$key] = $result['count'] ?? $result['size_mb'] ?? 0;
    }
} catch (PDOException $e) {
    // Si hay error, usar valores por defecto
    $estadisticas = [
        'total_eventos_activos' => 0,
        'total_inscripciones_mes' => 0,
        'espacio_uploads' => 0
    ];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración del Sistema - Admin FISEI</title>
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
                            Configuración del Sistema
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
                <!-- Estadísticas del sistema -->
                <div class="col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i>Estadísticas del Sistema
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-12 mb-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-primary mb-0"><?= $estadisticas['total_eventos_activos'] ?? 0 ?></h4>
                                        <small class="text-muted">Eventos Activos</small>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-success mb-0"><?= $estadisticas['total_inscripciones_mes'] ?? 0 ?></h4>
                                        <small class="text-muted">Total de inscripciones</small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <h4 class="text-info mb-0"><?= $estadisticas['espacio_uploads'] ?? 0 ?> MB</h4>
                                        <small class="text-muted">Espacio de archivos</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Información General -->
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-building me-2"></i>Información General
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="accion" value="actualizar_info_general">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nombre_institucion" class="form-label">Nombre de la Institución</label>
                                        <input type="text" class="form-control" id="nombre_institucion" name="nombre_institucion" 
                                               value="<?= htmlspecialchars($configuraciones['nombre_institucion'] ?? 'FISEI - UTA') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email_contacto" class="form-label">Email de Contacto</label>
                                        <input type="email" class="form-control" id="email_contacto" name="email_contacto" 
                                               value="<?= htmlspecialchars($configuraciones['email_contacto'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="telefono_contacto" class="form-label">Teléfono de Contacto</label>
                                        <input type="text" class="form-control" id="telefono_contacto" name="telefono_contacto" 
                                               value="<?= htmlspecialchars($configuraciones['telefono_contacto'] ?? '') ?>">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Guardar Información
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Límites del Sistema -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-sliders-h me-2"></i>Límites del Sistema
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="accion" value="actualizar_limites">
                                <div class="mb-3">
                                    <label for="max_inscripciones" class="form-label">Máximo de inscripciones por estudiante</label>
                                    <input type="number" class="form-control" id="max_inscripciones" name="max_inscripciones" 
                                           value="<?= htmlspecialchars($configuraciones['max_inscripciones_estudiante'] ?? '5') ?>" min="1" max="20">
                                    <div class="form-text">Número máximo de cursos a los que se puede inscribir un estudiante simultáneamente.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="dias_limite_cancelacion" class="form-label">Días límite para cancelar inscripción</label>
                                    <input type="number" class="form-control" id="dias_limite_cancelacion" name="dias_limite_cancelacion" 
                                           value="<?= htmlspecialchars($configuraciones['dias_limite_cancelacion'] ?? '3') ?>" min="0" max="30">
                                    <div class="form-text">Días antes del inicio del curso en que se puede cancelar la inscripción.</div>
                                </div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Actualizar Límites
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Herramientas de Mantenimiento -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-tools me-2"></i>Herramientas de Mantenimiento
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-warning" onclick="limpiarCacheSistema()">
                                    <i class="fas fa-broom me-2"></i>Limpiar Caché del Sistema
                                </button>
                                <button type="button" class="btn btn-info" onclick="exportarConfiguracion()">
                                    <i class="fas fa-download me-2"></i>Exportar Configuración
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="verLogsActividad()">
                                    <i class="fas fa-list me-2"></i>Ver Logs de Actividad
                                </button>
                                <hr>
                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Zona de Peligro:</strong> Las siguientes acciones pueden afectar el funcionamiento del sistema.
                                </div>
                                <button type="button" class="btn btn-danger" onclick="confirmarReinicioSistema()">
                                    <i class="fas fa-sync-alt me-2"></i>Reiniciar Sistema
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Información de Versión -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <strong>Versión del Sistema:</strong><br>
                                    <span class="badge bg-primary">v2.0.0</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Última Actualización:</strong><br>
                                    <small class="text-muted"><?= date('d/m/Y') ?></small>
                                </div>
                                <div class="col-md-3">
                                    <strong>Base de Datos:</strong><br>
                                    <span class="badge bg-success">MySQL <?= $conn->getAttribute(PDO::ATTR_SERVER_VERSION) ?></span>
                                </div>
                                <div class="col-md-3">
                                    <strong>PHP:</strong><br>
                                    <span class="badge bg-info">v<?= PHP_VERSION ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function limpiarCacheSistema() {
            if (confirm('¿Está seguro de que desea limpiar el caché del sistema?')) {
                alert('Función de limpieza de caché ejecutada (simulado)');
            }
        }
        
        function exportarConfiguracion() {
            alert('Exportando configuración... (función por implementar)');
        }
        
        function verLogsActividad() {
            alert('Mostrando logs de actividad... (función por implementar)');
        }
        
        function confirmarReinicioSistema() {
            if (confirm('¿ESTÁ SEGURO de que desea reiniciar el sistema? Esto afectará a todos los usuarios conectados.')) {
                if (confirm('Esta acción es irreversible. ¿Continuar?')) {
                    alert('Reiniciando sistema... (función por implementar)');
                }
            }
        }
    </script>
</body>
</html>
