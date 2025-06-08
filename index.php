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
  <?php
  require_once 'session.php';
  $nombre = getUserName();
  $apellido = getUserLastname();
  ?>

  <header class="top-header d-flex justify-content-between align-items-center px-4 py-2 shadow-sm --maroon">
    <div class="d-flex align-items-center">
      <img src="ruta/logo.png" alt="Logo institucional" style="height: 50px;">
      <div class="site-name ms-3 fw-bold">Gestión de Eventos Académicos - FISEI</div>
    </div>

    <div class="d-flex align-items-center gap-3">
      <?php if (isLoggedIn()): ?>
        <span class="fw-semibold">Hola, <?= htmlspecialchars(getUserName()) ?>
          <?= htmlspecialchars(getUserLastname()) ?></span>
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

  <footer class="footer-expandido">
    <div class="footer-container">
      <div class="footer-section">
        <h5>Sobre el sistema</h5>
        <ul>
          <li><a href="#"><i class="fa-solid fa-circle-question"></i> ¿Qué es Eventos FISEI?</a></li>
          <li><a href="#"><i class="fa-solid fa-book"></i> Manual de usuario</a></li>
          <li><a href="#"><i class="fa-solid fa-code-branch"></i> Versiones</a></li>
          <li><a href="informativo/nosotros.php"><i class="fa-solid fa-user-group"></i> Créditos</a></li>
        </ul>
      </div>

      <div class="footer-section">
        <h5>Soporte</h5>
        <ul>
          <li><a href="#"><i class="fa-solid fa-circle-info"></i> Preguntas frecuentes</a></li>
          <li><a href="formulario/solicitud_cambios.php"><i class="fa-solid fa-bug"></i> Reportar un error</a></li>
          <li><a href="#"><i class="fa-solid fa-headset"></i> Solicitar ayuda</a></li>
        </ul>
      </div>

      <div class="footer-section">
        <h5>Legal</h5>
        <ul>
          <li><a href="#"><i class="fa-solid fa-file-contract"></i> Términos de uso</a></li>
          <li><a href="#"><i class="fa-solid fa-user-shield"></i> Política de privacidad</a></li>
          <li><a href="#"><i class="fa-solid fa-scroll"></i> Licencia</a></li>
        </ul>
      </div>

      <div class="footer-section">
        <h5>FISEI - UTA</h5>
        <p>Facultad de Ingeniería en Sistemas,<br> Electrónica e Industrial</p>
        <div class="footer-social">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      &copy;
      <script>document.write(new Date().getFullYear());</script> FISEI - Universidad Técnica de Ambato. Todos los
      derechos reservados.
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      fetch('service/CursosPorCarrera.php')
        .then(response => {
          if (!response.ok) throw new Error('No autorizado o error de servidor');
          return response.json();
        })
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
            } else {
              botonHTML = `<a href="admin/administrar_evento.php?id=${curso.id}" class="btn btn-outline-secondary">Administrar</a>`;
            }

            contenedor.innerHTML += `
            <div class="card shadow-sm mb-4" style="max-width: 22rem;">
              <img src="${src}" class="card-img-top" alt="Imagen del evento">
              <div class="card-body">
                <h5 class="card-title text-maroon">${curso.nombre_evento}</h5>
                <p class="card-text mb-1"><strong>Fechas:</strong> ${curso.fecha_inicio} al ${curso.fecha_fin}</p>
                <p class="card-text mb-1"><strong>Ponente:</strong> ${curso.ponentes}</p>
                <p class="card-text mb-1"><strong>Horas académicas:</strong> ${curso.horas}</p>
                <p class="card-text mb-3"><strong>Cupos disponibles:</strong> ${curso.cupos}</p>
                <div class="text-center">
                  ${botonHTML}
                </div>
              </div>
            </div>
          `;
          });

          // Delegación de evento para los botones de inscripción
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

        })
        .catch(err => {
          document.getElementById('lista-cursos').innerHTML = `
          <div class="alert alert-danger">No se pudieron cargar los cursos: ${err.message}</div>
        `;
        });
    });
  </script>

</body>

</html>