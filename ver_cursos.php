<?php
require_once 'session.php';
$nombre = getUserName();
$apellido = getUserLastname();
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Inicio - Gestión de Eventos FISEI</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous">
</head>

<body class="bg-light">
  <header class="top-header d-flex justify-content-between align-items-center px-4 py-2 shadow-sm --maroon">
    <div class="d-flex align-items-center">
      <a href="index.php">
        <img src="resource/logo-universidad-tecnica-de-ambato.webp" alt="Logo institucional" style="height: 50px;">
      </a>
      <div class="site-name ms-3 fw-bold">Gestión de Eventos Académicos - FISEI</div>
    </div>

    <?php if (isLoggedIn() && getUserRole() === 'administrador'): ?>
    <button id="toggleSidebar" class="btn btn-outline-light">
      <i id="sidebarIcon" class="fas fa-bars" style="color: var(--maroon); font-size: 1.2rem;"></i>
    </button>

    <aside id="sidebar" class="sidebar hidden">
      <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom: 1px solid var(--gray-200);">
        <h5 style="color: var(--maroon-dark); font-weight: bold; margin: 0;">Menú de Administración</h5>
        <i id="closeSidebar" class="fas fa-xmark" style="color: var(--maroon); font-size: 1.4rem; cursor: pointer;"></i>
      </div>
      <ul class="nav flex-column px-4 py-2">
        <li class="nav-item"><a class="nav-link sidebar-link" href="views/cursos.php">Crear Curso</a></li>
        <li class="nav-item"><a class="nav-link sidebar-link" href="index.php">Administrar Curso</a></li>
        <li class="nav-item"><a class="nav-link sidebar-link" href="admin/crear_admin.php">Crear Administrador</a></li>
        <li class="nav-item"><a class="nav-link sidebar-link" href="admin/solicitudes_admin.php">Solicitudes de cambios</a></li>
        <li class="nav-item"><a class="nav-link sidebar-link" href="admin/comprobantes_pendientes.php">Aprobar comprobantes</a></li>
      </ul>
    </aside>
    <?php endif; ?>

    <div class="d-flex align-items-center gap-3">
      <?php if (isLoggedIn()): ?>
      <a href="perfil.php" class="fw-semibold text-white text-decoration-none">
        Hola, <?= htmlspecialchars(getUserName()) ?> <?= htmlspecialchars(getUserLastname()) ?>
      </a>
      <a href="logout.php" class="btn btn-white"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
      <?php else: ?>
      <a href="login.php" class="btn btn-white"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
      <a href="registro.php" class="btn btn-white"><i class="fas fa-user-plus"></i> Registrarse</a>
      <?php endif; ?>
    </div>
  </header>

  <main>
    <div class="container text-center mb-5">
      <h1>Gestión de Eventos Académicos - FISEI</h1>
      <p class="lead">Bienvenido al sistema de gestión de cursos y eventos académicos de la Facultad de Ingeniería en Sistemas, Electrónica e Industrial.</p>
    </div>

    <div class="container my-5">
      <div id="lista-cursos" class="row g-4"></div>
    </div>
  </main>

  <div class="modal fade" id="modalInscripcion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTituloCurso">Curso</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p><strong>Ponente:</strong> <span id="modalPonente"></span></p>
          <p><strong>Fechas:</strong> <span id="modalFechas"></span></p>
          <p><strong>Horas:</strong> <span id="modalHoras"></span></p>
          <p><strong>Cupos:</strong> <span id="modalCupos"></span></p>
          <h6>Requisitos:</h6>
          <ul id="lista-requisitos"></ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" id="btnConfirmarInscripcion">Confirmar inscripción</button>
        </div>
      </div>
    </div>
  </div>

  <footer class="footer-expandido mt-5">
    <div class="footer-container">
      <!-- contenido del footer -->
    </div>
    <div class="footer-bottom">
      © <?= date('Y') ?> FISEI - Universidad Técnica de Ambato. Todos los derechos reservados.
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const contenedor = document.getElementById('lista-cursos');

      fetch('service/CursosPorCarrera.php')
        .then(res => res.json())
        .then(data => {
          const { rol, cursos } = data;
          contenedor.innerHTML = '';

          cursos.forEach(curso => {
            const btn = (rol === 'estudiante' && !curso.inscrito)
              ? `<button class="btn btn-primary inscribirse-btn mt-3" data-id="${curso.id}">Inscribirse</button>`
              : (rol === 'administrador')
              ? `<a href="admin/administrar_evento.php?id=${curso.id}" class="btn btn-outline-secondary mt-3">Administrar</a>`
              : '';

            const tarjeta = document.createElement('div');
            tarjeta.className = 'col-md-4';
            tarjeta.innerHTML = `
              <div class="card h-100 shadow-sm">
                <img src="${(curso.ruta_imagen || 'resource/placeholder.svg').replace(/^(\.\.\/)+/, '')}" class="card-img-top" alt="Imagen del evento">
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title">${curso.nombre_evento}</h5>
                  <p><strong>Fechas:</strong> ${curso.fecha_inicio} al ${curso.fecha_fin}</p>
                  <p><strong>Ponente:</strong> ${curso.ponentes}</p>
                  <p><strong>Horas:</strong> ${curso.horas}</p>
                  <p><strong>Cupos:</strong> ${curso.cupos}</p>
                  <div class="mt-auto">${btn}</div>
                </div>
              </div>`;
            contenedor.appendChild(tarjeta);
          });
        });

      document.addEventListener('click', async e => {
        if (e.target.classList.contains('inscribirse-btn')) {
          const id = e.target.getAttribute('data-id');
          const res = await fetch('service/requisitos.php?evento_id=' + id);
          const data = await res.json();

          if (data.success) {
            const { curso, requisitos } = data;
            document.getElementById('modalTituloCurso').textContent = curso.nombre_evento;
            document.getElementById('modalPonente').textContent = curso.ponentes;
            document.getElementById('modalFechas').textContent = curso.fecha_inicio + ' al ' + curso.fecha_fin;
            document.getElementById('modalHoras').textContent = curso.horas;
            document.getElementById('modalCupos').textContent = curso.cupos;
            document.getElementById('lista-requisitos').innerHTML = requisitos.map(r =>
              `<li>${r.completado ? '✅' : '❌'} ${r.descripcion}</li>`).join('');
            document.getElementById('btnConfirmarInscripcion').setAttribute('data-id', id);

            const modal = new bootstrap.Modal(document.getElementById('modalInscripcion'));
            modal.show();
          }
        }
      });

      document.getElementById('btnConfirmarInscripcion').addEventListener('click', async () => {
        const eventoId = document.getElementById('btnConfirmarInscripcion').getAttribute('data-id');
        const res = await fetch('estudiantes/inscribirse_evento.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ evento_id: eventoId })
        });
        const result = await res.json();
        if (result.success) {
          alert('Inscripción realizada correctamente.');
          location.reload();
        } else {
          alert('Error al inscribirse: ' + result.message);
        }
      });

      const toggleBtn = document.getElementById('toggleSidebar');
      const sidebar = document.getElementById('sidebar');
      const closeBtn = document.getElementById('closeSidebar');
      if (toggleBtn && sidebar && closeBtn) {
        toggleBtn.addEventListener('click', () => sidebar.classList.toggle('hidden'));
        closeBtn.addEventListener('click', () => sidebar.classList.add('hidden'));
      }
    });
  </script>
</body>

</html>