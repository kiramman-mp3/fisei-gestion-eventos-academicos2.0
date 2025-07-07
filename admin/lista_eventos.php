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
$pagina = max(1, (int) ($_GET['pagina'] ?? 1));
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
    <link rel="stylesheet" href="../css/estilos.css">
</head>

<body>

    <header class="ctt-header">
        <div class="top-bar">
            <div class="logo">
                <img src="../uploads/logo.png" alt="Logo FISEI">
            </div>
            <div class="top-links">
                <div class="link-box">
                    <i class="fa-solid fa-arrow-left"></i>
                    <div>
                        <span class="title">Regresar</span><br>
                        <a href="javascript:history.back()">Regresa al Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="admin-container">

        <div class="header-admin mb-4">
            <div>
                <h2>Gestión de Eventos</h2>
                <small>Panel del Administrador</small>
            </div>
            <a href="crear_curso_p1.php" class="btn enviar">
                <i class="fas fa-plus me-2"></i> Crear Evento
            </a>
        </div>

        <!-- Filtros -->
        <div class="content-panel mb-4">
            <form method="GET" class="admin-form">
                <div class="admin-form-fields w-100">
                    <div class="form-row">
                        <div>
                            <label for="buscar">Buscar evento</label>
                            <input type="text" name="buscar" id="buscar" class="form-control-custom"
                                placeholder="Nombre del evento" value="<?= htmlspecialchars($buscar) ?>">
                        </div>
                        <div>
                            <label for="estado">Estado</label>
                            <select name="estado" id="estado" class="form-control-custom">
                                <option value="">Todos</option>
                                <option value="activo" <?= $filtro_estado === 'activo' ? 'selected' : '' ?>>Activos
                                </option>
                                <option value="finalizado" <?= $filtro_estado === 'finalizado' ? 'selected' : '' ?>>
                                    Finalizados</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-submit align-right mt-3">
                        <button type="submit" class="btn enviar">
                            <i class="fas fa-search me-2"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabla de eventos -->
        <div class="content-panel">
            <h3 class="mb-4"><i class="fas fa-calendar-check me-2"></i>Eventos encontrados: <?= $total_eventos ?></h3>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
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
                                        <br><small class="text-muted"><i class="fas fa-image me-1"></i>Con imagen</small>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($evento['tipo_evento'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($evento['categoria'] ?? 'N/A') ?></td>
                                <td>
                                    <small>
                                        <strong>Inicio:</strong>
                                        <?= date('d/m/Y', strtotime($evento['fecha_inicio'])) ?><br>
                                        <strong>Fin:</strong> <?= date('d/m/Y', strtotime($evento['fecha_fin'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge-admin"><?= $evento['total_inscritos'] ?> /
                                        <?= $evento['cupos'] ?></span>
                                </td>
                                <td>
                                    <?php
                                    $color = match ($evento['estado_evento']) {
                                        'Finalizado' => 'bg-secondary',
                                        'En curso' => 'bg-success',
                                        'Próximo' => 'bg-primary',
                                        default => 'bg-dark'
                                    };
                                    ?>
                                    <span class="badge <?= $color ?>"><?= $evento['estado_evento'] ?></span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="administrar_evento.php?id=<?= $evento['id'] ?>"
                                            class="btn btn-outline-primary" title="Administrar"><i
                                                class="fas fa-cogs"></i></a>
                                        <a href="pdf_evento.php?id=<?= $evento['id'] ?>" target="_blank"
                                            class="btn btn-outline-primary" title="PDF"><i class="fas fa-file-pdf"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($eventos)): ?>
                            <tr>
                                <td colspan="8" class="text-center">
                                    <p class="text-muted">No se encontraron eventos.</p>
                                    <a href="crear_curso_p1.php" class="btn enviar mt-3">
                                        <i class="fas fa-plus me-2"></i> Crear primer evento
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php if ($total_paginas > 1): ?>
                <div class="form-submit align-right mt-4">
                    <nav>
                        <ul class="pagination">
                            <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link"
                                    href="?pagina=<?= $pagina - 1 ?>&estado=<?= urlencode($filtro_estado) ?>&buscar=<?= urlencode($buscar) ?>">Anterior</a>
                            </li>
                            <?php for ($i = max(1, $pagina - 2); $i <= min($total_paginas, $pagina + 2); $i++): ?>
                                <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                    <a class="page-link"
                                        href="?pagina=<?= $i ?>&estado=<?= urlencode($filtro_estado) ?>&buscar=<?= urlencode($buscar) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $pagina >= $total_paginas ? 'disabled' : '' ?>">
                                <a class="page-link"
                                    href="?pagina=<?= $pagina + 1 ?>&estado=<?= urlencode($filtro_estado) ?>&buscar=<?= urlencode($buscar) ?>">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>