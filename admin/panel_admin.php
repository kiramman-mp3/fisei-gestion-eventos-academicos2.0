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

    // Contar inscripciones pendientes de aprobación (asumiendo que esto es 'solicitudes pendientes')
    $stmt = $conn->query("SELECT COUNT(*) as solicitudes_pendientes FROM inscripciones WHERE estado = 'Esperando aprobación del admin'");
    $solicitudes_pendientes = $stmt->fetch(PDO::FETCH_ASSOC)['solicitudes_pendientes'];

    // Contar comprobantes pendientes (asumiendo una tabla o campo para esto)
    // Suponiendo una columna 'estado_comprobante' en 'inscripciones' o una tabla 'pagos'
    $stmt = $conn->query("SELECT COUNT(*) as comprobantes_pendientes FROM inscripciones WHERE estado_comprobante = 'Pendiente'");
    $comprobantes_pendientes = $stmt->fetch(PDO::FETCH_ASSOC)['comprobantes_pendientes'];


    // Contar resoluciones pendientes
    $stmt = $conn->query("SELECT COUNT(*) as resoluciones_pendientes FROM resoluciones WHERE estado != 'Terminado'");
    $resoluciones_pendientes = $stmt->fetch(PDO::FETCH_ASSOC)['resoluciones_pendientes'];

    // Total de tareas pendientes (inscripciones + resoluciones)
    $total_pendientes = $solicitudes_pendientes + $resoluciones_pendientes; // Ajustado para incluir solicitudes y resoluciones

} catch (PDOException $e) {
    $error = "Error al obtener estadísticas: " . $e->getMessage();
    $total_eventos = 0;
    $total_estudiantes = 0;
    $total_admins = 0;
    $total_pendientes = 0;
    $solicitudes_pendientes = 0;
    $comprobantes_pendientes = 0;
    $resoluciones_pendientes = 0;
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
    <link rel="stylesheet" href="../css/estilos.css">

<body>
    <div class="panel-container">
        <div class="col-md-3 col-lg-2 sidebar">
            <div class="sidebar-header">
                <img src="../uploads/logo.png" alt="Logo FISEI" class="img-fluid logo">
                <h5 class="mb-0">Panel Admin</h5>
                <small>FISEI - UTA</small>
            </div>

            <nav class="nav flex-column sidebar-nav">
                <a class="nav-link active" href="panel_admin.php">
                    <i class="fas fa-tachometer-alt"></i>Dashboard
                </a>
                <a class="nav-link" href="crear_curso_p1.php">
                    <i class="fas fa-plus"></i>Crear Curso
                </a>
                <a class="nav-link" href="../ver_cursos.php">
                    <i class="fas fa-graduation-cap"></i>Ver Cursos
                </a>
                <a class="nav-link" href="lista_eventos.php">
                    <i class="fas fa-edit"></i>Administrar Eventos
                </a>
                <a class="nav-link" href="gestionar_usuarios.php">
                    <i class="fas fa-users"></i>Gestionar Usuarios
                </a>
                <a class="nav-link" href="solicitudes_admin.php">
                    <i class="fas fa-file-alt"></i>Solicitudes
                </a>
                <a class="nav-link" href="comprobantes_pendientes.php">
                    <i class="fas fa-receipt"></i>Comprobantes
                </a>
                <a class="nav-link" href="configuracion_sistema.php">
                    <i class="fas fa-cog"></i>Configuración
                </a>
                <a class="nav-link" href="../administrar_pagina/admin.php">
                    <i class="fas fa-globe"></i>Administrar Página
                </a>
                <hr>
                <a class="nav-link" href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i>Cerrar Sesión
                </a>
            </nav>
        </div>

        <div class="col-md-9 col-lg-10 main-content">
            <div class="header-admin">
                <div>
                    <h2 class="mb-0">Bienvenido, <?= htmlspecialchars(getUserName() . ' ' . getUserLastname()) ?></h2>
                    <small>Panel de Administración - FISEI</small>
                </div>
                <div>
                    <span class="badge badge-admin">Administrador</span>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card admin-card">
                        <div class="card-body">
                            <i class="fas fa-plus-circle card-icon text-primary"></i>
                            <h5 class="card-title">Crear Nuevo Curso</h5>
                            <p class="card-text">Crea y configura un nuevo evento académico o curso.</p>
                            <a href="crear_curso_p1.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i>Crear Curso
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card admin-card">
                        <div class="card-body">
                            <i class="fas fa-list card-icon text-success"></i>
                            <h5 class="card-title">Ver Cursos</h5>
                            <p class="card-text">Visualiza y administra todos los cursos existentes.</p>
                            <a href="../ver_cursos.php" class="btn btn-success">
                                <i class="fas fa-eye"></i>Ver Cursos
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card admin-card">
                        <div class="card-body">
                            <i class="fas fa-edit card-icon text-warning"></i>
                            <h5 class="card-title">Administrar Eventos</h5>
                            <p class="card-text">Edita y gestiona los eventos académicos existentes.</p>
                            <a href="lista_eventos.php" class="btn btn-warning">
                                <i class="fas fa-list"></i>Ver Lista
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card admin-card">
                        <div class="card-body">
                            <i class="fas fa-users-cog card-icon text-info"></i>
                            <h5 class="card-title">Gestionar Usuarios</h5>
                            <p class="card-text">Administra estudiantes y otros administradores del sistema.</p>
                            <a href="gestionar_usuarios.php" class="btn btn-info">
                                <i class="fas fa-users-cog"></i>Gestionar
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card admin-card">
                        <div class="card-body">
                            <i class="fas fa-file-alt card-icon text-secondary"></i>
                            <h5 class="card-title">Solicitudes</h5>
                            <p class="card-text">Revisa y gestiona las solicitudes de requisitos.</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="solicitudes_admin.php" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-list"></i>Ver
                                </a>
                                <?php if (($solicitudes_pendientes ?? 0) > 0): ?>
                                    <span class="badge bg-danger"><?= $solicitudes_pendientes ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card admin-card">
                        <div class="card-body">
                            <i class="fas fa-receipt card-icon text-danger"></i>
                            <h5 class="card-title">Comprobantes</h5>
                            <p class="card-text">Aprueba o rechaza los comprobantes de pago.</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="comprobantes_pendientes.php" class="btn btn-danger btn-sm">
                                    <i class="fas fa-check"></i>Revisar
                                </a>
                                <?php if (($comprobantes_pendientes ?? 0) > 0): ?>
                                    <span class="badge bg-warning"><?= $comprobantes_pendientes ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card admin-card">
                        <div class="card-body">
                            <i class="fas fa-gavel card-icon text-secondary"></i>
                            <h5 class="card-title">Resoluciones</h5>
                            <p class="card-text">Gestiona y visualiza las resoluciones de eventos.</p>
                            <a href="ver_resoluciones.php" class="btn btn-secondary">
                                <i class="fas fa-eye"></i>Ver Resoluciones
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card admin-card">
                        <div class="card-body">
                            <i class="fas fa-cogs card-icon text-dark"></i>
                            <h5 class="card-title">Configuración</h5>
                            <p class="card-text">Configura parámetros generales del sistema.</p>
                            <a href="configuracion_sistema.php" class="btn btn-dark">
                                <i class="fas fa-cogs"></i>Configurar
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card admin-card">
                        <div class="card-body">
                            <i class="fas fa-globe card-icon text-primary"></i>
                            <h5 class="card-title">Administrar Página</h5>
                            <p class="card-text">Gestiona el contenido del sitio web (carrusel, información, etc.).</p>
                            <a href="../administrar_pagina/admin.php" class="btn btn-primary">
                                <i class="fas fa-edit"></i>Administrar
                            </a>
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
        document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
            link.addEventListener('click', function (e) {
                // Remover clase active de todos los links
                document.querySelectorAll('.sidebar-nav .nav-link').forEach(l => l.classList.remove('active'));
                // Agregar clase active al link clickeado
                this.classList.add('active');
            });
        });

        // Se elimina la función updateDateTime ya que no hay un elemento con id 'datetime'
        // Si quieres mostrar la fecha y hora en alguna parte del panel,
        // deberás agregar un elemento HTML con el id 'datetime' y luego descomentar la función.
    </script>
</body>

</html>