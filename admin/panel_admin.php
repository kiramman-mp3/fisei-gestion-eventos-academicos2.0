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

// Obtener estadísticas para el panel
try {
    // Contar total de eventos (cursos/eventos)
    $stmt = $conn->query("SELECT COUNT(*) as total_eventos FROM eventos");
    $total_eventos = $stmt->fetch(PDO::FETCH_ASSOC)['total_eventos'];

    // Contar total de estudiantes
    $stmt = $conn->query("SELECT COUNT(*) as total_estudiantes FROM estudiantes WHERE rol = 'estudiante'");
    $total_estudiantes = $stmt->fetch(PDO::FETCH_ASSOC)['total_estudiantes'];

    // Contar administradores
    $stmt = $conn->query("SELECT COUNT(*) as total_admins FROM estudiantes WHERE rol = 'administrador'");
    $total_admins = $stmt->fetch(PDO::FETCH_ASSOC)['total_admins'];

    // Contar inscripciones pendientes de aprobación
    $stmt = $conn->query("SELECT COUNT(*) as inscripciones_pendientes FROM inscripciones WHERE estado = 'Esperando aprobación del admin'");
    $inscripciones_pendientes = $stmt->fetch(PDO::FETCH_ASSOC)['inscripciones_pendientes'];

    // Contar resoluciones pendientes
    $stmt = $conn->query("SELECT COUNT(*) as resoluciones_pendientes FROM resoluciones WHERE estado != 'Terminado'");
    $resoluciones_pendientes = $stmt->fetch(PDO::FETCH_ASSOC)['resoluciones_pendientes'];

    // Total de tareas pendientes (inscripciones + resoluciones)
    $total_pendientes = $inscripciones_pendientes + $resoluciones_pendientes;

} catch (PDOException $e) {
    $error = "Error al obtener estadísticas: " . $e->getMessage();
    $total_eventos = 0;
    $total_estudiantes = 0;
    $total_admins = 0;
    $total_pendientes = 0;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - FISEI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/panel-estilos.css">
    <style>
        .admin-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
        }
        
        .sidebar {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            min-height: 100vh;
            color: white;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            margin: 0.2rem 0;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        .header-admin {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-4">
                    <div class="text-center mb-4">
                        <img src="../uploads/logo.png" alt="Logo FISEI" class="img-fluid mb-2" style="max-height: 60px;">
                        <h5 class="mb-0">Panel Admin</h5>
                        <small class="text-light opacity-75">FISEI - UTA</small>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="panel_admin.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="crear_curso_p1.php">
                            <i class="fas fa-plus me-2"></i>Crear Curso
                        </a>
                        <a class="nav-link" href="../ver_cursos.php">
                            <i class="fas fa-graduation-cap me-2"></i>Ver Cursos
                        </a>
                        <a class="nav-link" href="lista_eventos.php">
                            <i class="fas fa-edit me-2"></i>Administrar Eventos
                        </a>
                        <a class="nav-link" href="gestionar_usuarios.php">
                            <i class="fas fa-users me-2"></i>Gestionar Usuarios
                        </a>
                        <a class="nav-link" href="solicitudes_admin.php">
                            <i class="fas fa-file-alt me-2"></i>Solicitudes
                        </a>
                        <a class="nav-link" href="comprobantes_pendientes.php">
                            <i class="fas fa-receipt me-2"></i>Comprobantes
                        </a>
                        <a class="nav-link" href="configuracion_sistema.php">
                            <i class="fas fa-cog me-2"></i>Configuración
                        </a>
                        <a class="nav-link" href="../administrar_pagina/admin.php">
                            <i class="fas fa-globe me-2"></i>Administrar Página
                        </a>
                        <hr class="text-light">
                        <a class="nav-link" href="../logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Header -->
                <div class="header-admin p-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">Bienvenido, <?= htmlspecialchars(getUserName() . ' ' . getUserLastname()) ?></h2>
                            <small class="text-muted">Panel de Administración - FISEI</small>
                        </div>
                        <div>
                            <span class="badge bg-success">Administrador</span>
                        </div>
                    </div>
                </div>
                
                <!-- Dashboard Section -->
                <div id="dashboard-content">
                    <!-- Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-graduation-cap card-icon"></i>
                                    <div class="stats-number"><?= $total_eventos ?? 0 ?></div>
                                    <div>Total Eventos</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-users card-icon"></i>
                                    <div class="stats-number"><?= $total_estudiantes ?? 0 ?></div>
                                    <div>Estudiantes</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-shield card-icon"></i>
                                    <div class="stats-number"><?= $total_admins ?? 0 ?></div>
                                    <div>Administradores</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-exclamation-triangle card-icon"></i>
                                    <div class="stats-number"><?= $total_pendientes ?? 0 ?></div>
                                    <div>Pendientes</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Acciones principales -->
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card admin-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-plus-circle card-icon text-primary"></i>
                                    <h5 class="card-title">Crear Nuevo Curso</h5>
                                    <p class="card-text">Crea y configura un nuevo evento académico o curso.</p>
                                    <a href="crear_curso_p1.php" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Crear Curso
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card admin-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-list card-icon text-success"></i>
                                    <h5 class="card-title">Ver Cursos</h5>
                                    <p class="card-text">Visualiza y administra todos los cursos existentes.</p>
                                    <a href="../ver_cursos.php" class="btn btn-success">
                                        <i class="fas fa-eye me-2"></i>Ver Cursos
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card admin-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-edit card-icon text-warning"></i>
                                    <h5 class="card-title">Administrar Eventos</h5>
                                    <p class="card-text">Edita y gestiona los eventos académicos existentes.</p>
                                    <a href="lista_eventos.php" class="btn btn-warning">
                                        <i class="fas fa-list me-2"></i>Ver Lista
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card admin-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-users-cog card-icon text-info"></i>
                                    <h5 class="card-title">Gestionar Usuarios</h5>
                                    <p class="card-text">Administra estudiantes y otros administradores del sistema.</p>
                                    <a href="gestionar_usuarios.php" class="btn btn-info">
                                        <i class="fas fa-users-cog me-2"></i>Gestionar
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card admin-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-alt card-icon text-secondary"></i>
                                    <h5 class="card-title">Solicitudes</h5>
                                    <p class="card-text">Revisa y gestiona las solicitudes de requisitos.</p>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="solicitudes_admin.php" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-list me-1"></i>Ver
                                        </a>
                                        <?php if (($solicitudes_pendientes ?? 0) > 0): ?>
                                            <span class="badge bg-danger"><?= $solicitudes_pendientes ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card admin-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-receipt card-icon text-danger"></i>
                                    <h5 class="card-title">Comprobantes</h5>
                                    <p class="card-text">Aprueba o rechaza los comprobantes de pago.</p>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="comprobantes_pendientes.php" class="btn btn-danger btn-sm">
                                            <i class="fas fa-check me-1"></i>Revisar
                                        </a>
                                        <?php if (($comprobantes_pendientes ?? 0) > 0): ?>
                                            <span class="badge bg-warning"><?= $comprobantes_pendientes ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card admin-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-clipboard-list card-icon text-primary"></i>
                                    <h5 class="card-title">Requisitos</h5>
                                    <p class="card-text">Gestiona los requisitos necesarios para los cursos.</p>
                                    <a href="gestionar_requisitos.php" class="btn btn-primary">
                                        <i class="fas fa-cogs me-2"></i>Gestionar
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card admin-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-star card-icon text-success"></i>
                                    <h5 class="card-title">Notas</h5>
                                    <p class="card-text">Gestiona las calificaciones de los estudiantes.</p>
                                    <a href="gestionar_notas.php" class="btn btn-success">
                                        <i class="fas fa-star me-2"></i>Gestionar
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card admin-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-gavel card-icon text-secondary"></i>
                                    <h5 class="card-title">Resoluciones</h5>
                                    <p class="card-text">Gestiona y visualiza las resoluciones de eventos.</p>
                                    <a href="ver_resoluciones.php" class="btn btn-secondary">
                                        <i class="fas fa-eye me-2"></i>Ver Resoluciones
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card admin-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-cogs card-icon text-dark"></i>
                                    <h5 class="card-title">Configuración</h5>
                                    <p class="card-text">Configura parámetros generales del sistema.</p>
                                    <a href="configuracion_sistema.php" class="btn btn-dark">
                                        <i class="fas fa-cogs me-2"></i>Configurar
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card admin-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-globe card-icon text-primary"></i>
                                    <h5 class="card-title">Administrar Página</h5>
                                    <p class="card-text">Gestiona el contenido del sitio web (carrusel, información, etc.).</p>
                                    <a href="../administrar_pagina/admin.php" class="btn btn-primary">
                                        <i class="fas fa-edit me-2"></i>Administrar
                                    </a>
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
        // Funcionalidad para el sidebar
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                // Remover clase active de todos los links
                document.querySelectorAll('.sidebar .nav-link').forEach(l => l.classList.remove('active'));
                // Agregar clase active al link clickeado
                this.classList.add('active');
            });
        });
        
        // Mostrar fecha y hora actual
        function updateDateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric', 
                hour: '2-digit', 
                minute: '2-digit' 
            };
            document.getElementById('datetime').textContent = now.toLocaleDateString('es-ES', options);
        }
        
        // Actualizar cada minuto
        setInterval(updateDateTime, 60000);
        updateDateTime();
    </script>
</body>
</html>
