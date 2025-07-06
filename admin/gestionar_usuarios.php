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

// Manejar acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $usuario_id = $_POST['usuario_id'] ?? '';
    
    try {
        switch ($accion) {
            case 'eliminar':
                $stmt = $conn->prepare("DELETE FROM estudiantes WHERE id = :id AND id != :current_id");
                $stmt->execute([':id' => $usuario_id, ':current_id' => getUserId()]);
                $mensaje = "Usuario eliminado correctamente.";
                $tipo_mensaje = "success";
                break;
                
            case 'cambiar_rol':
                $nuevo_rol = $_POST['nuevo_rol'] ?? '';
                if (in_array($nuevo_rol, ['estudiante', 'administrador'])) {
                    $stmt = $conn->prepare("UPDATE estudiantes SET rol = :rol WHERE id = :id AND id != :current_id");
                    $stmt->execute([':rol' => $nuevo_rol, ':id' => $usuario_id, ':current_id' => getUserId()]);
                    $mensaje = "Rol actualizado correctamente.";
                    $tipo_mensaje = "success";
                }
                break;
                
            case 'toggle_estado':
                $stmt = $conn->prepare("UPDATE estudiantes SET verificado = NOT verificado WHERE id = :id");
                $stmt->execute([':id' => $usuario_id]);
                $mensaje = "Estado del usuario actualizado.";
                $tipo_mensaje = "success";
                break;
        }
    } catch (PDOException $e) {
        $mensaje = "Error: " . $e->getMessage();
        $tipo_mensaje = "danger";
    }
}

// Obtener parámetros de filtro
$filtro_rol = $_GET['rol'] ?? '';
$buscar = $_GET['buscar'] ?? '';
$pagina = max(1, (int)($_GET['pagina'] ?? 1));
$por_pagina = 10;
$offset = ($pagina - 1) * $por_pagina;

// Construir consulta
$where_conditions = [];
$params = [];

if ($filtro_rol) {
    $where_conditions[] = "rol = :rol";
    $params[':rol'] = $filtro_rol;
}

if ($buscar) {
    $where_conditions[] = "(nombre LIKE :buscar OR apellido LIKE :buscar OR correo LIKE :buscar)";
    $params[':buscar'] = "%$buscar%";
}

$where_clause = $where_conditions ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Obtener usuarios
$query = "SELECT * FROM estudiantes $where_clause ORDER BY fecha_registro DESC LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar total de usuarios para paginación
$count_query = "SELECT COUNT(*) as total FROM estudiantes $where_clause";
$stmt = $conn->prepare($count_query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$total_usuarios = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ceil($total_usuarios / $por_pagina);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Admin FISEI</title>
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
                            Gestión de Usuarios
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
            
            <!-- Filtros y búsqueda -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <label for="buscar" class="form-label">Buscar usuario</label>
                                    <input type="text" class="form-control" id="buscar" name="buscar" 
                                           value="<?= htmlspecialchars($buscar) ?>" 
                                           placeholder="Nombre, Apellido o Email">
                                </div>
                                <div class="col-md-3">
                                    <label for="rol" class="form-label">Filtrar por rol</label>
                                    <select class="form-select" id="rol" name="rol">
                                        <option value="">Todos los roles</option>
                                        <option value="estudiante" <?= $filtro_rol === 'estudiante' ? 'selected' : '' ?>>Estudiante</option>
                                        <option value="administrador" <?= $filtro_rol === 'administrador' ? 'selected' : '' ?>>Administrador</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-2"></i>Buscar
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid">
                                        <a href="crear_admin.php" class="btn btn-success">
                                            <i class="fas fa-user-plus me-2"></i>Nuevo Admin
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabla de usuarios -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-users me-2"></i>
                                Lista de Usuarios (<?= $total_usuarios ?> total)
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre Completo</th>
                                            <th>Email</th>
                                            <th>Rol</th>
                                            <th>Carrera</th>
                                            <th>Estado</th>
                                            <th>Fecha Registro</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($usuarios as $usuario): ?>
                                            <tr>
                                                <td><?= $usuario['id'] ?></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></strong>
                                                </td>
                                                <td><?= htmlspecialchars($usuario['correo']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $usuario['rol'] === 'administrador' ? 'danger' : 'primary' ?>">
                                                        <?= ucfirst($usuario['rol']) ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($usuario['carrera'] ?? 'N/A') ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $usuario['verificado'] ? 'success' : 'warning' ?>">
                                                        <?= $usuario['verificado'] ? 'Verificado' : 'Pendiente' ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></td>
                                                <td>
                                                    <?php if ($usuario['id'] !== getUserId()): ?>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <!-- Cambiar rol -->
                                                            <button type="button" class="btn btn-outline-warning" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#cambiarRolModal"
                                                                    data-user-id="<?= $usuario['id'] ?>"
                                                                    data-user-name="<?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?>"
                                                                    data-current-role="<?= $usuario['rol'] ?>">
                                                                <i class="fas fa-user-tag"></i>
                                                            </button>
                                                            
                                                            <!-- Toggle estado -->
                                                            <form method="POST" style="display: inline;">
                                                                <input type="hidden" name="accion" value="toggle_estado">
                                                                <input type="hidden" name="usuario_id" value="<?= $usuario['id'] ?>">
                                                                <button type="submit" class="btn btn-outline-<?= $usuario['verificado'] ? 'secondary' : 'success' ?>"
                                                                        onclick="return confirm('¿Cambiar el estado de verificación de este usuario?')">
                                                                    <i class="fas fa-<?= $usuario['verificado'] ? 'user-slash' : 'user-check' ?>"></i>
                                                                </button>
                                                            </form>
                                                            
                                                            <!-- Eliminar -->
                                                            <form method="POST" style="display: inline;">
                                                                <input type="hidden" name="accion" value="eliminar">
                                                                <input type="hidden" name="usuario_id" value="<?= $usuario['id'] ?>">
                                                                <button type="submit" class="btn btn-outline-danger"
                                                                        onclick="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="badge bg-info">Tu cuenta</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        
                                        <?php if (empty($usuarios)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No se encontraron usuarios</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Paginación -->
                        <?php if ($total_paginas > 1): ?>
                            <div class="card-footer">
                                <nav aria-label="Paginación de usuarios">
                                    <ul class="pagination justify-content-center mb-0">
                                        <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?pagina=<?= $pagina - 1 ?>&rol=<?= urlencode($filtro_rol) ?>&buscar=<?= urlencode($buscar) ?>">
                                                Anterior
                                            </a>
                                        </li>
                                        
                                        <?php for ($i = max(1, $pagina - 2); $i <= min($total_paginas, $pagina + 2); $i++): ?>
                                            <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                                <a class="page-link" href="?pagina=<?= $i ?>&rol=<?= urlencode($filtro_rol) ?>&buscar=<?= urlencode($buscar) ?>">
                                                    <?= $i ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <li class="page-item <?= $pagina >= $total_paginas ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?pagina=<?= $pagina + 1 ?>&rol=<?= urlencode($filtro_rol) ?>&buscar=<?= urlencode($buscar) ?>">
                                                Siguiente
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para cambiar rol -->
    <div class="modal fade" id="cambiarRolModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Rol de Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="cambiar_rol">
                        <input type="hidden" name="usuario_id" id="modal-user-id">
                        
                        <p>¿Cambiar el rol del usuario <strong id="modal-user-name"></strong>?</p>
                        
                        <div class="mb-3">
                            <label for="nuevo_rol" class="form-label">Nuevo rol:</label>
                            <select class="form-select" name="nuevo_rol" id="nuevo_rol" required>
                                <option value="estudiante">Estudiante</option>
                                <option value="administrador">Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Cambiar Rol</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Configurar modal de cambio de rol
        document.addEventListener('DOMContentLoaded', function() {
            const cambi            Panel de Administración
            ├── Sidebar Navigation
            │   ├── Dashboard
            │   ├── Crear Curso
            │   ├── Ver Cursos
            │   ├── Administrar Eventos
            │   ├── Gestionar Usuarios
            │   ├── Solicitudes
            │   ├── Comprobantes
            │   └── Configuración
            ├── Dashboard Principal
            │   ├── Estadísticas (4 tarjetas)
            │   └── Acceso Rápido (12 funciones)
            └── Páginas Especializadas
                ├── Gestión de Usuarios
                └── Configuración del SistemaarRolModal = document.getElementById('cambiarRolModal');
            cambiarRolModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user-id');
                const userName = button.getAttribute('data-user-name');
                const currentRole = button.getAttribute('data-current-role');
                
                document.getElementById('modal-user-id').value = userId;
                document.getElementById('modal-user-name').textContent = userName;
                document.getElementById('nuevo_rol').value = currentRole === 'estudiante' ? 'administrador' : 'estudiante';
            });
        });
    </script>
</body>
</html>
