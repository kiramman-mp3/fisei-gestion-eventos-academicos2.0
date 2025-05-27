<?php
session_start();
include('sql/conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $correo = $_POST['correo'];
  $password = $_POST['password'];

  $query = $conexion->prepare("SELECT * FROM estudiantes WHERE correo = ?");
  $query->bind_param("s", $correo);
  $query->execute();
  $result = $query->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
      $_SESSION['user'] = $user;
      header("Location: perfil.php");
      exit;
    }
  }

  $error = "Correo o contraseña incorrectos";
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
    <img src="img/logo_uta.png" alt="Logo UTA">
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

  <footer>
    © <?= date('Y') ?> FISEI - Universidad Técnica de Ambato
  </footer>

</body>

</html>