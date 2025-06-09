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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    crossorigin="anonymous">
</head>

<body class="bg-light">


  <header class="top-header d-flex justify-content-between align-items-center px-4 py-2 shadow-sm --maroon">
    <div class="d-flex align-items-center">
      <a href="index.php">
        <img src="resource/logo-universidad-tecnica-de-ambato.webp" alt="Logo institucional" style="height: 50px;">
      </a>
      <div class="site-name ms-3 fw-bold">Gestión de Eventos Académicos - FISEI</div>
    </div>

    <?php if (isLoggedIn() && getUserRole() === 'admimistrador'): ?>
      <!-- Botón para abrir el sidebar solo para administradores -->
      <button id="toggleSidebar" class="btn btn-white d-flex align-items-center justify-content-center"
        style="padding: 8px 18px;">
        <i id="sidebarIcon" class="fas fa-bars" style="color: var(--maroon); font-size: 1.2rem;"></i>
      </button>

      <!-- Sidebar visible solo para administradores -->
      <aside id="sidebar" class="sidebar hidden">
        <div class="d-flex align-items-center justify-content-between px-4 py-3"
          style="border-bottom: 1px solid var(--gray-200);">
          <h5 style="color: var(--maroon-dark); font-weight: bold; margin: 0;">Menú de Administración</h5>
          <i id="closeSidebar" class="fas fa-xmark" style="color: var(--maroon); font-size: 1.4rem; cursor: pointer;"></i>
        </div>
        <ul class="nav flex-column px-4 py-2">
          <li class="nav-item"><a class="nav-link sidebar-link" href="views/cursos.php">Crear Curso</a></li>
          <li class="nav-item"><a class="nav-link sidebar-link" href="index.php">Administrar Curso</a></li>
          <li class="nav-item"><a class="nav-link sidebar-link" href="admin/crear_admin.php">Crear Administrador</a>
          </li>
          <li class="nav-item"><a class="nav-link sidebar-link" href="admin/solicitudes_admin.php">Solicitudes de
              cambios</a>
          </li>
          <li class="nav-item"><a class="nav-link sidebar-link" href="admin/comprobantes_pendientes.php">Aprobar
              comprobantes/a>
              </li>
        </ul>
      </aside>
    <?php endif; ?>

    <div class="d-flex align-items-center gap-3">
      <?php if (isLoggedIn()): ?>
        <a href="perfil.php" class="fw-semibold text-white text-decoration-none">
          Hola, <?= htmlspecialchars(getUserName()) ?>   <?= htmlspecialchars(getUserLastname()) ?>
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
      <p class="lead">Bienvenido al sistema de gestión de cursos y eventos académicos de la Facultad de Ingeniería en
        Sistemas, Electrónica e Industrial.</p>
    </div>

    <div class="container my-5">
      <div id="lista-cursos" class="cards-grid"></div>
    </div>
  </main>

  <footer class="footer-expandido mt-5">
    <div class="footer-container">
      <div class="footer-section">
        <h5>Sobre el sistema</h5>
        <ul>
          <li><a href="informativo/que_es_eventos.php"><i class="fa-solid fa-circle-question"></i> ¿Qué es Eventos
              FISEI?</a></li>
          <li><a href="informativo/manual_usuario.php"><i class="fa-solid fa-book"></i> Manual de usuario</a></li>
          <li><a href="informativo/versiones.php"><i class="fa-solid fa-code-branch"></i> Versiones</a></li>
          <li><a href="informativo/nosotros.php"><i class="fa-solid fa-user-group"></i> Créditos</a></li>
        </ul>
      </div>
      <div class="footer-section">
        <h5>Soporte</h5>
        <ul>
          <li><a href="informativo/preguntas_frecuentes.php"><i class="fa-solid fa-circle-info"></i> Preguntas
              frecuentes</a></li>
          <li><a href="formulario/solicitud_cambios.php"><i class="fa-solid fa-bug"></i> Reportar un error</a></li>
          <li><a href="formulario/solicitar_ayuda.php"><i class="fa-solid fa-headset"></i> Solicitar ayuda</a></li>
        </ul>
      </div>
      <div class="footer-section">
        <h5>Legal</h5>
        <ul>
          <li><a href="legal/terminos_uso.php"><i class="fa-solid fa-file-contract"></i> Términos de uso</a></li>
          <li><a href="legal/politica_privacidad.php"><i class="fa-solid fa-user-shield"></i> Política de
              privacidad</a></li>
          <li><a href="legal/licencia.php"><i class="fa-solid fa-scroll"></i> Licencia</a></li>
        </ul>
      </div>
      <div class="footer-section">
        <h5>FISEI - UTA</h5>
        <p>Facultad de Ingeniería en Sistemas,<br> Electrónica e Industrial</p>
        <div class="footer-social">
          <a href="https://www.facebook.com/UTAFISEI"><i class="fab fa-facebook-f"></i></a>
          <a href="https://www.instagram.com/fisei_uta"><i class="fab fa-instagram"></i></a>
          <a
            href="https://www.linkedin.com/pub/dir?firstName=Fisei&lastName=uta&trk=people-guest_people-search-bar_search-submit"><i
              class="fab fa-linkedin-in"></i></a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      © <?= date('Y') ?> FISEI - Universidad Técnica de Ambato. Todos los derechos reservados.
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      fetch('service/CursosPorCarrera.php')
        .then(response => response.json())
        .then(data => {
          const { rol, cursos } = data;
          const contenedor = document.getElementById('lista-cursos');
          contenedor.innerHTML = '';

          cursos.forEach(curso => {
            const rutaCruda = curso.ruta_imagen || '';
            const rutaLimpia = rutaCruda.replace(/^\.\.\/+/, '');
            const src = rutaLimpia !== '' ? rutaLimpia : 'resource/placeholder.svg';

            let botonHTML = '';
            if (rol === 'estudiante') {
              botonHTML = `<button class="btn btn-primary inscribirse-btn" data-id="${curso.id}">Inscribirse</button>`;
            } else if (rol === 'admimistrador') {
              botonHTML = `<a href="admin/administrar_evento.php?id=${curso.id}" class="btn btn-outline-secondary">Administrar</a>`;
            }

            // Si no hay rol, no se muestra ningún botón

            contenedor.innerHTML += `
          <div class="card shadow-sm mb-4" style="max-width: 22rem;">
            <img src="${src}" class="card-img-top" alt="Imagen del evento">
            <div class="card-body">
              <h5 class="card-title text-maroon">${curso.nombre_evento}</h5>
              <p class="card-text mb-1"><strong>Fechas:</strong> ${curso.fecha_inicio} al ${curso.fecha_fin}</p>
              <p class="card-text mb-1"><strong>Ponente:</strong> ${curso.ponentes}</p>
              <p class="card-text mb-1"><strong>Horas académicas:</strong> ${curso.horas}</p>
              <p class="card-text mb-3"><strong>Cupos disponibles:</strong> ${curso.cupos}</p>
              ${botonHTML ? `<div class="text-center">${botonHTML}</div>` : ''}
            </div>
          </div>
        `;
          });

          // Activar solo si hay botones de inscripción
          if (rol === 'estudiante') {
            document.querySelectorAll('.inscribirse-btn').forEach(btn => {
              btn.addEventListener('click', () => {
                const eventoId = btn.getAttribute('data-id');
                fetch('estudiantes/inscribirse_evento.php', {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json'
                  },
                  body: JSON.stringify({ evento_id: eventoId })
                })
                  .then(res => res.json())
                  .then(response => {
                    if (response.success) {
                      alert('Inscripción realizada correctamente.');
                      location.reload();
                    } else {
                      alert('Error al inscribirse: ' + response.message);
                    }
                  })
                  .catch(err => alert('Error de red: ' + err.message));
              });
            });
          }
        })
        .catch(err => {
          document.getElementById('lista-cursos').innerHTML = `
        <div class="alert alert-danger">No se pudieron cargar los cursos: ${err.message}</div>
      `;
        });
    });

  document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const closeBtn = document.getElementById('closeSidebar');

    if (toggleBtn && sidebar && closeBtn) {
      toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('hidden');
      });

      closeBtn.addEventListener('click', () => {
        sidebar.classList.add('hidden');
      });
    }
  });


</script>

</body>

</html>