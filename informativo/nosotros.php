<?php
  require_once '../session.php';
  $nombre = getUserName();
  $apellido = getUserLastname();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Créditos</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header class="top-header d-flex justify-content-between align-items-center px-4 py-2 shadow-sm --maroon">
    <div class="d-flex align-items-center">
    <a href="../index.php">
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
    <h1 class="display-5">Créditos</h1>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <div class="col">
        <div class="card h-100">
          <img src="../resource/pablo.png" class="card-img-top" alt="Pablo Vayas">
          <div class="card-body">
            <h5 class="card-title">Pablo Vayas</h5>
            <p class="card-text">Estudiante de Software, especializado en frontend y bases de datos...</p>
            <div class="d-flex gap-2 mt-3">
              <a href="https://www.facebook.com/pablo.vayas.33" class="btn btn-outline-primary" title="Facebook"><i class="fab fa-facebook-f"></i></a>
              <a href="https://www.instagram.com/pablo.vayas/" class="btn btn-outline-danger" title="Instagram"><i class="fab fa-instagram"></i></a>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100">
          <img src="../resource/alexis.jpg" class="card-img-top" alt="Alexis López">
          <div class="card-body">
            <h5 class="card-title">Alexis López</h5>
            <p class="card-text">Apasionado por la programación web y el diseño visual interactivo...</p>
            <div class="d-flex gap-2 mt-3">
              <a href="https://www.facebook.com/alexis.lopez.737521" class="btn btn-outline-primary" title="Facebook"><i class="fab fa-facebook-f"></i></a>
              <a href="https://www.instagram.com/alexislp.z/" class="btn btn-outline-danger" title="Instagram"><i class="fab fa-instagram"></i></a>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100">
          <img src="../resource/jose.jpg" class="card-img-top" alt="José Manzano">
          <div class="card-body">
            <h5 class="card-title">José Manzano</h5>
            <p class="card-text">Estudiante de Software con interés en computación gráfica y desarrollo de videojuegos...</p>
            <div class="d-flex gap-2 mt-3">
              <a href="https://www.facebook.com/profile.php?id=100036996780282" class="btn btn-outline-primary" title="Facebook"><i class="fab fa-facebook-f"></i></a>
              <a href="https://www.instagram.com/manzano8555/" class="btn btn-outline-danger" title="Instagram"><i class="fab fa-instagram"></i></a>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100">
          <img src="../resource/johan.jpg" class="card-img-top" alt="Johan Rodriguez">
          <div class="card-body">
            <h5 class="card-title">Johan Rodriguez</h5>
            <p class="card-text">Estudiante de Software con experiencia en simulaciones gráficas y sistemas interactivos...</p>
            <div class="d-flex gap-2 mt-3">
              <a href="https://www.facebook.com/johan.kiramman/" class="btn btn-outline-primary" title="Facebook"><i class="fab fa-facebook-f"></i></a>
              <a href="https://www.instagram.com/jhnrx907/" class="btn btn-outline-danger" title="Instagram"><i class="fab fa-instagram"></i></a>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100">
          <img src="../resource/alan.jpg" class="card-img-top" alt="Alan Puruncajas">
          <div class="card-body">
            <h5 class="card-title">Alan Puruncajas</h5>
            <p class="card-text">Estudiante apasionado por el diseño gráfico y la realidad aumentada...</p>
            <div class="d-flex gap-2 mt-3">
              <a href="https://www.facebook.com/alan.puruncajas" class="btn btn-outline-primary" title="Facebook"><i class="fab fa-facebook-f"></i></a>
              <a href="https://www.instagram.com/alam_cuenquita/" class="btn btn-outline-danger" title="Instagram"><i class="fab fa-instagram"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
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
          <li><a href="../formulario/solicitud_cambios.php"><i class="fa-solid fa-bug"></i> Reportar un error</a></li>
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