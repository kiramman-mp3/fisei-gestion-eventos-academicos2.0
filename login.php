<?php
session_start();
include('sql/conexion.php');
include('session.php'); // Incluir el archivo de sesión para usar las funciones auxiliares

$cris = new Conexion();
$conexion = $cris->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $correo = $_POST['correo'];
  $password = $_POST['password'];

  try {
    $query = $conexion->prepare("SELECT * FROM estudiantes WHERE correo = :correo");
    $query->execute([':correo' => $correo]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['usuario_id'] = $user['id'];
      $_SESSION['email'] = $user['correo'];
      $_SESSION['nombre'] = $user['nombre'];
      $_SESSION['apellido'] = $user['apellido'];
      $_SESSION['rol'] = $user['rol'];
      $_SESSION['carrera'] = $user['carrera'] ?? null;
      header("Location: index.php");
      exit;
    } else {
      $error = "Correo o contraseña incorrectos";
    }
  } catch (PDOException $e) {
    $error = "Error al conectar a la base de datos: " . $e->getMessage();
  }
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Login - Eventos FISEI</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/styles.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    html,
    body {
      height: 100%;
    }

    body {
      display: flex;
      flex-direction: column;
    }

    main {
      flex: 2;
    }

    .login-card {
      max-width: 600px;
    }
  </style>
</head>

<body>

  <header class="top-header">
    <img src="resource/logo-uta.png" alt="Logo UTA">
    <div class="site-name">Eventos Académicos FISEI</div>
  </header>

  <main class="card-custom login-card">
    <div class="text-center mb-4">
      <h1>Iniciar Sesión</h1>
      <p class="text-muted">Accede con tu correo registrado</p>
    </div>

    <?php if (isset($error)): ?>
      <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label for="correo">Correo:</label>
        <input type="email" name="correo" id="correo" required>
      </div>

      <div class="mb-3">
        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required>
      </div>

      <div class="text-center">
        <button type="submit" class="btn btn-primary">Ingresar</button>
      </div>

      <div class="text-center mt-3">
        <a href="registro.php" class="btn btn-outline-primary text-decoration-none">¿No tienes cuenta? Regístrate
          aquí</a>
      </div>
    </form>
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