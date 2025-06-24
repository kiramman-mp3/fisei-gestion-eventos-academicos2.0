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
  <link rel="stylesheet" href="css/estilos.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    crossorigin="anonymous">
</head>

<body class="bg-light">


  <header class="ctt-header">
    <div class="top-bar">
      <div class="logo">
        <img src="uploads/logo.png" alt="Logo CTT">
        <?php if (isLoggedIn()): ?>
          <div class="user-greeting">
            <i class="fas fa-hand-peace"></i>
            Hola, <strong><?= htmlspecialchars(getUserName()) ?>   <?= htmlspecialchars(getUserLastname()) ?></strong>
          </div>
        <?php endif; ?>
      </div>
      <?php if (isLoggedIn()): ?>
        <div class="top-links">
          <div class="link-box">
            <i class="fas fa-desktop"></i>
            <div>
              <span class="title">Pagina informativa</span><br>
              <a href="index.php">Mira aquí</a>
            </div>
          </div>
          <div class="link-box">
            <i class="fas fa-user"></i>
            <div>
              <span class="title">Perfil</span><br>
              <a href="perfil.php">Ver mi perfil</a>
            </div>
          </div>
          <div class="link-box">
            <i class="fas fa-sign-out-alt"></i>
            <div>
              <span class="title">Cerrar sesión</span><br>
              <a href="logout.php">Cierra sesión aquí</a>
            </div>
          </div>

        <?php else: ?>
          <div class="top-links">
            <div class="link-box">
              <i class="fas fa-desktop"></i>
              <div>
                <span class="title">Pagina informativa</span><br>
                <a href="index.php">Mira aquí</a>
              </div>
            </div>
            <div class="link-box">
              <i class="fas fa-user"></i>
              <div>
                <span class="title">Iniciar sesión</span><br>
                <a href="login.php">Ingresa aquí</a>
              </div>
            </div>

            <div class="link-box">
              <i class="fa-solid fa-plus"></i>
              <div>
                <span class="title">Regístrate</span><br>
                <a href="registro.php">Crea tu cuenta</a>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
  </header>
  <main>
    <div class="container my-5">
      <h1 class="text-center mb-4">Cursos Disponibles</h1>
      <p class="text-center text-muted mb-4">Explora nuestros cursos y eventos disponibles.</p>

      <div class="container my-5">
        <div id="lista-cursos" class="cards-grid"></div>
      </div>
    </div>
  </main>


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
              botonHTML = `<button class="boton-inscribirse inscribirse-btn" data-id="${curso.id}">Inscribirse</button>`;
            } else if (rol === 'admimistrador') {
              botonHTML = `<a href="admin/administrar_evento.php?id=${curso.id}" class="boton-inscribirse" style="background-color:#eee; color:#333;">Administrar</a>`;
            }

            contenedor.innerHTML += `
          <div class="card-curso">
            <img src="${src}" alt="Imagen del evento">
            <div class="card-body">
              <h5>${curso.nombre_evento}</h5>
              <p><strong>Fechas:</strong> ${curso.fecha_inicio} al ${curso.fecha_fin}</p>
              <p><strong>Ponente:</strong> ${curso.ponentes}</p>
              <p><strong>Horas académicas:</strong> ${curso.horas}</p>
              <p><strong>Cupos disponibles:</strong> ${curso.cupos}</p>
              ${botonHTML ? `<div class="text-center mt-3">${botonHTML}</div>` : ''}
            </div>
          </div>
        `;
          });

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