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

// Obtener parámetros de filtro
$filtro_estado = $_GET['estado'] ?? '';
$buscar = $_GET['buscar'] ?? '';
$pagina = max(1, (int)($_GET['pagina'] ?? 1));
$por_pagina = 10;
$offset = ($pagina - 1) * $por_pagina;

// Construir consulta
$where_conditions = [];
$params = [];

if ($filtro_estado) {
    if ($filtro_estado === 'activo') {
        $where_conditions[] = "e.fecha_fin >= CURDATE()";
    } else if ($filtro_estado === 'finalizado') {
        $where_conditions[] = "e.fecha_fin < CURDATE()";
    }
}

if ($buscar) {
    $where_conditions[] = "e.nombre_evento LIKE :buscar";
    $params[':buscar'] = "%$buscar%";
}

$where_clause = $where_conditions ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Obtener eventos con información adicional
$query = "
    SELECT e.*, 
           t.nombre AS tipo_evento, 
           c.nombre AS categoria,
           (SELECT COUNT(*) FROM inscripciones WHERE evento_id = e.id) as total_inscritos,
           CASE 
               WHEN e.fecha_fin < CURDATE() THEN 'Finalizado'
               WHEN e.fecha_inicio <= CURDATE() AND e.fecha_fin >= CURDATE() THEN 'En curso'
               ELSE 'Próximo'
           END as estado_evento
    FROM eventos e
    LEFT JOIN tipos_evento t ON e.tipo_evento_id = t.id
    LEFT JOIN categorias_evento c ON e.categoria_id = c.id
    $where_clause
    ORDER BY e.fecha_inicio DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $conn->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar total de eventos para paginación
$count_query = "SELECT COUNT(*) as total FROM eventos e $where_clause";
$stmt = $conn->prepare($count_query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$total_eventos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ceil($total_eventos / $por_pagina);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Eventos - Admin FISEI</title>
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
                            Lista de Eventos para Administrar
                        </span>
                    </div>
                </nav>
            </div>
        </div>
        
        <div class="container-fluid mt-4">            
            <!-- Filtros y búsqueda -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <label for="buscar" class="form-label">Buscar evento</label>
                                    <input type="text" class="form-control" id="buscar" name="buscar" 
                                           value="<?= htmlspecialchars($buscar) ?>" 
                                           placeholder="Nombre del evento">
                                </div>
                                <div class="col-md-3">
                                    <label for="estado" class="form-label">Filtrar por estado</label>
                                    <select class="form-select" id="estado" name="estado">
                                        <option value="">Todos los estados</option>
                                        <option value="activo" <?= $filtro_estado === 'activo' ? 'selected' : '' ?>>Activos</option>
                                        <option value="finalizado" <?= $filtro_estado === 'finalizado' ? 'selected' : '' ?>>Finalizados</option>
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
                                        <a href="crear_curso_p1.php" class="btn btn-success">
                                            <i class="fas fa-plus me-2"></i>Crear Evento
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabla de eventos -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar-check me-2"></i>
                                Lista de Eventos (<?= $total_eventos ?> total)
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre del Evento</th>
                                            <th>Tipo</th>
                                            <th>Categoría</th>
                                            <th>Fechas</th>
                                            <th>Inscritos</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($eventos as $evento): ?>
                                            <tr>
                                                <td><?= $evento['id'] ?></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($evento['nombre_evento']) ?></strong>
                                                    <?php if (!empty($evento['ruta_imagen'])): ?>
                                                        <small class="d-block text-muted">
                                                            <i class="fas fa-image me-1"></i>Con imagen
                                                        </small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($evento['tipo_evento'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($evento['categoria'] ?? 'N/A') ?></td>
                                                <td>
                                                    <small>
                                                        <strong>Inicio:</strong> <?= date('d/m/Y', strtotime($evento['fecha_inicio'])) ?><br>
                                                        <strong>Fin:</strong> <?= date('d/m/Y', strtotime($evento['fecha_fin'])) ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?= $evento['total_inscritos'] ?> / <?= $evento['cupos'] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $badge_class = '';
                                                    switch($evento['estado_evento']) {
                                                        case 'Finalizado':
                                                            $badge_class = 'bg-secondary';
                                                            break;
                                                        case 'En curso':
                                                            $badge_class = 'bg-success';
                                                            break;
                                                        case 'Próximo':
                                                            $badge_class = 'bg-primary';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="badge <?= $badge_class ?>">
                                                        <?= $evento['estado_evento'] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <!-- Administrar evento -->
                                                        <a href="administrar_evento.php?id=<?= $evento['id'] ?>" 
                                                           class="btn btn-primary" 
                                                           title="Administrar evento">
                                                            <i class="fas fa-cogs"></i>
                                                        </a>
                                                        
                                                        <!-- Ver evento público -->
                                                        <a href="../ver_cursos.php#evento-<?= $evento['id'] ?>" 
                                                           class="btn btn-info" 
                                                           title="Ver evento público"
                                                           target="_blank">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        
                                                        <!-- Generar PDF -->
                                                        <a href="pdf_evento.php?id=<?= $evento['id'] ?>" 
                                                           class="btn btn-success" 
                                                           title="Generar PDF"
                                                           target="_blank">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                        
                                                        <!-- Editar evento -->
                                                        <a href="editar_evento.php?id=<?= $evento['id'] ?>" 
                                                           class="btn btn-warning" 
                                                           title="Editar evento">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        
                                        <?php if (empty($eventos)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No se encontraron eventos</p>
                                                    <a href="crear_curso_p1.php" class="btn btn-primary">
                                                        <i class="fas fa-plus me-2"></i>Crear primer evento
                                                    </a>
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
                                <nav aria-label="Paginación de eventos">
                                    <ul class="pagination justify-content-center mb-0">
                                        <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?pagina=<?= $pagina - 1 ?>&estado=<?= urlencode($filtro_estado) ?>&buscar=<?= urlencode($buscar) ?>">
                                                Anterior
                                            </a>
                                        </li>
                                        
                                        <?php for ($i = max(1, $pagina - 2); $i <= min($total_paginas, $pagina + 2); $i++): ?>
                                            <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                                <a class="page-link" href="?pagina=<?= $i ?>&estado=<?= urlencode($filtro_estado) ?>&buscar=<?= urlencode($buscar) ?>">
                                                    <?= $i ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <li class="page-item <?= $pagina >= $total_paginas ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?pagina=<?= $pagina + 1 ?>&estado=<?= urlencode($filtro_estado) ?>&buscar=<?= urlencode($buscar) ?>">
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
