<?php
session_start();
require_once 'config.php';
$_SESSION['rol'] = 'administracion'; // Simulación de rol para pruebas, eliminar en producción
$rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : null;
if (!$rol || !in_array($rol, ['estudiante', 'docente', 'administracion'])) {
  header("Location: " . BASE_URL . "login.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Administrativo - UTA</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/styles.css" />
</head>

<body>

  <header class="bg-uta-red text-white py-3 px-4 d-flex align-items-center justify-content-between shadow-sm">
    <!-- Botón hamburguesa integrado -->
    <button id="toggleSidebar" class="btn btn-white d-flex align-items-center justify-content-center"
      style="padding: 8px 18px;">
      <i id="sidebarIcon" class="fas fa-bars" style="color: var(--maroon); font-size: 1.2rem;"></i>
    </button> <!-- Sidebar personalizado -->
    <aside id="sidebar" class="sidebar hidden">
      <div class="d-flex align-items-center justify-content-between px-4 py-3"
        style="border-bottom: 1px solid var(--gray-200);">
        <h5 style="color: var(--maroon-dark); font-weight: bold; margin: 0;">Opciones de navegación</h5>
        <i id="closeSidebar" class="fas fa-xmark" style="color: var(--maroon); font-size: 1.4rem; cursor: pointer;"></i>
      </div>
      <ul class="nav flex-column px-4 py-2">
        <?php if ($rol === 'estudiante'): ?>
          <li class="nav-item"><a class="nav-link sidebar-link" href="estudiantes/mis_cursos.php">Mis Cursos</a></li>
          <li class="nav-item"><a class="nav-link sidebar-link" href="estudiantes/perfil_estudiante.php">Mi Perfil</a>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link sidebar-link" href="views/cursos.html">Crear Curso</a></li>
          <li class="nav-item"><a class="nav-link sidebar-link" href="index.php">Administrar Curso</a></li>
          <li class="nav-item"><a class="nav-link sidebar-link" href="admin/crear_admin.html">Crear Administrador</a></li>
          <li class="nav-item"><a class="nav-link sidebar-link" href="admin/solicitudes_admin.php">Solicitudes de
              cambios</a></li>
          <li class="nav-item"><a class="nav-link sidebar-link" href="admin/comprobantes_pendientes.php">Aprobar
              comprobantes</a></li>
        <?php endif; ?>
      </ul>
    </aside> <!-- Títulos -->
    <div class="text-end flex-grow-1 ms-3">
      <h1 class="mb-0" style="color: white; font-size: 1.4rem;">Panel de Control</h1>
      <p class="mb-0" style="font-weight: 600;">Universidad Técnica de Ambato</p>
    </div>
  </header>
  <div id="mainContent" class="transition-container">
    <main class="container py-5">
      <div class="row g-4">
        <?php if ($rol === 'estudiante'): ?>
          <!-- Estudiante: solo ve cursos y perfil -->
          <div class="col-md-6">
            <div class="card h-100">
              <div class="card-body d-flex flex-column text-center justify-content-center">
                <div class="icon-box"><i class="bi bi-journal-bookmark-fill"></i></div>
                <h5 class="card-title">Mis Cursos</h5>
                <p class="card-text">Consulta tus eventos inscritos y sus requisitos.</p>
                <div class="d-grid gap-2">
                  <a href="estudiantes/mis_cursos.php" class="btn btn-outline-primary">Ver Cursos</a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card h-100">
              <div class="card-body d-flex flex-column text-center justify-content-center">
                <div class="icon-box"><i class="bi bi-person-lines-fill"></i></div>
                <h5 class="card-title">Mi Perfil</h5>
                <p class="card-text">Actualiza tus datos personales e institucionales.</p>
                <div class="d-grid gap-2">
                  <a href="perfil.php" class="btn btn-outline-primary">Ver Perfil</a>
                </div>
              </div>
            </div>
          </div>

        <?php else: ?>
          <!-- Docente y Administración -->
          <!-- Crear Curso -->
          <div class="col-md-4">
            <div class="card h-100">
              <div class="card-body d-flex flex-column text-center justify-content-center">
                <div class="icon-box"><i class="bi bi-plus-square-fill"></i></div>
                <h5 class="card-title">Crear Curso</h5>
                <p class="card-text">Crea un nuevo curso académico con requisitos personalizados.</p>
                <div class="d-grid gap-2">
                  <a href="views/cursos.html" class="btn btn-outline-primary">Crear Curso</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Administrar Curso -->
          <div class="col-md-4">
            <div class="card h-100">
              <div class="card-body d-flex flex-column text-center justify-content-center">
                <div class="icon-box"><i class="bi bi-gear-fill"></i></div>
                <h5 class="card-title">Administrar Curso</h5>
                <p class="card-text">Gestiona inscripciones, evaluaciones y documentación de los cursos.</p>
                <div class="d-grid gap-2">
                  <a href="index.php" class="btn btn-outline-primary">Administrar</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Crear Administrador -->
          <div class="col-md-4">
            <div class="card h-100">
              <div class="card-body d-flex flex-column text-center justify-content-center">
                <div class="icon-box"><i class="bi bi-person-plus-fill"></i></div>
                <h5 class="card-title">Crear Administrador</h5>
                <p class="card-text">Registra nuevos administradores del sistema de eventos.</p>
                <div class="d-grid gap-2">
                  <a href="admin/crear_admin.html" class="btn btn-outline-primary">Crear Admin</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Solicitudes de Cambio -->
          <div class="col-md-6">
            <div class="card h-100">
              <div class="card-body d-flex flex-column text-center justify-content-center">
                <div class="icon-box"><i class="bi bi-file-earmark-diff-fill"></i></div>
                <h5 class="card-title">Solicitudes</h5>
                <p class="card-text">Revisa y responde las solicitudes de cambio enviadas por los usuarios.</p>
                <div class="d-grid gap-2">
                  <a href="admin/solicitudes_admin.php" class="btn btn-outline-primary">Ver Solicitudes</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Aprobación de Comprobantes -->
          <div class="col-md-6">
            <div class="card h-100">
              <div class="card-body d-flex flex-column text-center justify-content-center">
                <div class="icon-box"><i class="bi bi-receipt-cutoff"></i></div>
                <h5 class="card-title">Aprobar Comprobantes</h5>
                <p class="card-text">Valida los pagos pendientes enviados por los estudiantes.</p>
                <div class="d-grid gap-2">
                  <a href="admin/comprobantes_pendientes.php" class="btn btn-outline-primary">Aprobar</a>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </main>
  </div>



  <footer class="footer-expandido mt-5">
    <div class="footer-container">
      <div class="footer-section">
        <h5>Sobre el sistema</h5>
        <ul>
          <li><a href="../informativo/que_es_eventos.php"><i class="fa-solid fa-circle-question"></i> ¿Qué es Eventos
              FISEI?</a></li>
          <li><a href="../informativo/manual_usuario.php"><i class="fa-solid fa-book"></i> Manual de usuario</a></li>
          <li><a href="../informativo/versiones.php"><i class="fa-solid fa-code-branch"></i> Versiones</a></li>
          <li><a href="../informativo/nosotros.php"><i class="fa-solid fa-user-group"></i> Créditos</a></li>
        </ul>
      </div>

      <div class="footer-section">
        <h5>Soporte</h5>
        <ul>
          <li><a href="../informativo/preguntas_frecuentes.php"><i class="fa-solid fa-circle-info"></i> Preguntas
              frecuentes</a></li>
          <li><a href="../formulario/solictud_cambios.php"><i class="fa-solid fa-bug"></i> Reportar un error</a></li>
          <li><a href="../formulario/solicitar_ayuda.php"><i class="fa-solid fa-headset"></i> Solicitar ayuda</a></li>
        </ul>
      </div>

      <div class="footer-section">
        <h5>Legal</h5>
        <ul>
          <li><a href="../legal/terminos_uso.php"><i class="fa-solid fa-file-contract"></i> Términos de uso</a></li>
          <li><a href="../legal/politica_privacidad.php"><i class="fa-solid fa-user-shield"></i> Política de
              privacidad</a></li>
          <li><a href="../legal/licencia.php"><i class="fa-solid fa-scroll"></i> Licencia</a></li>
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
      ©
      <?= date('Y') ?> FISEI - Universidad Técnica de Ambato. Todos los derechos reservados.>
    </div>
  </footer>
  <script>
    const toggleBtn = document.getElementById("toggleSidebar");
    const closeBtn = document.getElementById("closeSidebar");
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("mainContent");

    toggleBtn.addEventListener("click", () => {
      sidebar.classList.remove("hidden");
      sidebar.classList.add("visible");
      mainContent.classList.add("sidebar-open");
    });

    closeBtn.addEventListener("click", () => {
      sidebar.classList.remove("visible");
      sidebar.classList.add("hidden");
      mainContent.classList.remove("sidebar-open");
    });
  </script>


</body>

</html>