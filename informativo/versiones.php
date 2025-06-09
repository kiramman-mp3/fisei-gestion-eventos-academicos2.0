<?php
  require_once 'session.php';
  $nombre = getUserName();
  $apellido = getUserLastname();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Versiones</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <header class="top-header d-flex justify-content-between align-items-center px-4 py-2 shadow-sm --maroon">
    <div class="d-flex align-items-center">
      <a href="../index.php">
        <img src="ruta/logo.png" alt="Logo institucional" style="height: 50px;">
      </a>
      <div class="site-name ms-3 fw-bold">Gestión de Eventos Académicos - FISEI</div>
    </div>

    <div class="d-flex align-items-center gap-3">
      <?php if (isLoggedIn()): ?>
        <span class="fw-semibold">Hola, <?= htmlspecialchars(getUserName()) ?> <?= htmlspecialchars(getUserLastname()) ?></span>
        <a href="logout.php" class="btn btn-white"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-white"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
        <a href="registro.php" class="btn btn-white"><i class="fas fa-user-plus"></i> Registrarse</a>
      <?php endif; ?>
    </div>
  </header>

  <main class="container mt-4">
    <h1 class="display-5">Versiones</h1>
    <p class="fs-5">
      A continuación, se presenta un historial con las versiones del sistema, incluyendo mejoras implementadas, nuevas funcionalidades añadidas y correcciones realizadas.
    </p>
    <ul class="fs-5">
      <li><strong>v1.0</strong> - Lanzamiento inicial con funcionalidades básicas de registro, login, inscripción y generación de certificados.</li>
      <li><strong>v1.1</strong> - Mejoras en la interfaz de usuario y correcciones menores en la validación de formularios.</li>
      <li><strong>v1.2</strong> - Se añadió soporte para revisión de requisitos por evento y se optimizó el flujo de inscripción.</li>
      <li><strong>v2.0</strong> - Integración de roles avanzados, nuevos reportes, rediseño completo del dashboard y sistema de notificaciones.</li>
    </ul>
  </main>

  <footer class="footer-expandido mt-5">
    <div class="footer-container">
      <div class="footer-section">
        <h5>Sobre el sistema</h5>
        <ul>
          <li><a href="../informativo/que_es_eventos.php"><i class="fa-solid fa-circle-question"></i> ¿Qué es Eventos FISEI?</a></li>
          <li><a href="../informativo/manual_usuario.php"><i class="fa-solid fa-book"></i> Manual de usuario</a></li>
          <li><a href="../informativo/versiones.php"><i class="fa-solid fa-code-branch"></i> Versiones</a></li>
          <li><a href="../informativo/nosotros.php"><i class="fa-solid fa-user-group"></i> Créditos</a></li>
        </ul>
      </div>

      <div class="footer-section">
        <h5>Soporte</h5>
        <ul>
          <li><a href="../informativo/preguntas_frecuentes.php"><i class="fa-solid fa-circle-info"></i> Preguntas frecuentes</a></li>
          <li><a href="../formulario/solictud_cambios.php"><i class="fa-solid fa-bug"></i> Reportar un error</a></li>
          <li><a href="../formulario/solicitar_ayuda.php"><i class="fa-solid fa-headset"></i> Solicitar ayuda</a></li>
        </ul>
      </div>

      <div class="footer-section">
        <h5>Legal</h5>
        <ul>
          <li><a href="../legal/terminos_uso.php"><i class="fa-solid fa-file-contract"></i> Términos de uso</a></li>
          <li><a href="../legal/politica_privacidad.php"><i class="fa-solid fa-user-shield"></i> Política de privacidad</a></li>
          <li><a href="../legal/licencia.php"><i class="fa-solid fa-scroll"></i> Licencia</a></li>
        </ul>
      </div>

      <div class="footer-section">
        <h5>FISEI - UTA</h5>
        <p>Facultad de Ingeniería en Sistemas,<br> Electrónica e Industrial</p>
        <div class="footer-social">
          <a href="https://www.facebook.com/UTAFISEI"><i class="fab fa-facebook-f"></i></a>
          <a href="https://www.instagram.com/fisei_uta"><i class="fab fa-instagram"></i></a>
          <a href="https://www.linkedin.com/pub/dir?firstName=Fisei&lastName=uta&trk=people-guest_people-search-bar_search-submit"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      © <?= date('Y') ?> FISEI - Universidad Técnica de Ambato. Todos los derechos reservados.
    </div>
  </footer>
</body>
</html>
