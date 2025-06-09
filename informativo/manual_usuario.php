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
  <title>Manual de Usuario</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<header class="top-header d-flex justify-content-between align-items-center px-4 py-2 shadow-sm --maroon">
    <div class="d-flex align-items-center">
    <a href="index.php">
  <img src="../resource/logo-universidad-tecnica-de-ambato.webp" alt="Logo institucional" style="height: 50px;">
</a>
      <div class="site-name ms-3 fw-bold">Gestión de Eventos Académicos - FISEI</div>
    </div>

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

  <main class="container mt-4">
    <h1 class="display-5">Manual de Usuario</h1>
    <p class="fs-5">
      Este manual tiene como objetivo guiar al usuario en el uso de la plataforma "Gestión de Eventos Académicos - FISEI". Aquí encontrará las instrucciones paso a paso para navegar por el sistema, desde el registro hasta la obtención de certificados.
    </p>
    <ol class="fs-5">
      <li><strong>Registro e Inicio de Sesión:</strong> Los usuarios deben registrarse con su correo institucional o personal. Posteriormente, podrán iniciar sesión para acceder a los servicios del sistema.</li>
      <li><strong>Inscripción a eventos:</strong> Una vez dentro del sistema, podrá visualizar los eventos disponibles. Al seleccionar uno, se mostrará la descripción, los requisitos y la opción para inscribirse.</li>
      <li><strong>Subida de requisitos:</strong> Algunos eventos solicitan documentos previos como cédula, certificados u otros. Podrá cargarlos desde la sección de inscripción.</li>
      <li><strong>Revisión del estado:</strong> Puede monitorear el estado de sus inscripciones, saber si han sido aprobadas o si falta algún requisito.</li>
      <li><strong>Asistencia y notas:</strong> El sistema permite consultar si su asistencia ha sido registrada, así como las calificaciones obtenidas en caso de haber evaluaciones.</li>
      <li><strong>Descarga de certificados:</strong> Al finalizar un evento aprobado, podrá descargar su certificado directamente desde la plataforma en formato PDF.</li>
    </ol>
    <p class="fs-5">
      Si en algún momento tiene dificultades, puede acceder a las opciones de soporte en el pie de página para reportar errores o solicitar ayuda personalizada.
    </p>
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
