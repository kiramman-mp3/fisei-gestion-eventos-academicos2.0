<?php
require_once 'sql/conexion.php';
require_once 'session.php';

$cris = new Conexion();
$conexion = $cris->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = $_POST['nombre'];
  $apellido = $_POST['apellido'];
  $correo = $_POST['correo'];
  $cedula = $_POST['cedula'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $genero = $_POST['genero'];
  $fecha_nacimiento = $_POST['anio'] . '-' . $_POST['mes'] . '-' . $_POST['dia'];

  // Validar tipo de correo
  if (str_ends_with($correo, '@uta.edu.ec')) {
    $tipo = 'institucional';
  } elseif (preg_match('/@.+\..+/', $correo)) {
    $tipo = 'publico';
  } else {
    $error = "Correo inválido.";
  }

  if (!isset($error)) {
    try {
      $stmt = $conexion->prepare("INSERT INTO estudiantes 
        (nombre, apellido, correo, password, cedula, genero, fecha_nacimiento, tipo)
        VALUES (:nombre, :apellido, :correo, :password, :cedula, :genero, :fecha_nacimiento, :tipo)");

      $stmt->bindValue(':nombre', $nombre);
      $stmt->bindValue(':apellido', $apellido);
      $stmt->bindValue(':correo', $correo);
      $stmt->bindValue(':password', $password);
      $stmt->bindValue(':cedula', $cedula);
      $stmt->bindValue(':genero', $genero);
      $stmt->bindValue(':fecha_nacimiento', $fecha_nacimiento);
      $stmt->bindValue(':tipo', $tipo);

      if ($stmt->execute()) {
        // Login automático tras registro
        $query = $conexion->prepare("SELECT * FROM estudiantes WHERE correo = :correo LIMIT 1");
        $query->bindValue(':correo', $correo);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user) {
          $_SESSION['usuario_id'] = $user['id'];
          $_SESSION['nombre']     = $user['nombre'];
          $_SESSION['apellido']   = $user['apellido'];
          $_SESSION['email']      = $user['correo'];
          $_SESSION['rol']        = $user['rol'];
          $_SESSION['carrera'] = $user['carrera'] ?? null;
          header("Location: index.php");
          exit;
        }
      } else {
        $error = "Este correo ya está registrado.";
      }
    } catch (PDOException $e) {
      $error = "Error en el registro: " . $e->getMessage();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro Estudiante</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/styles.css?v=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    html, body { height: 100%; }
    body { display: flex; flex-direction: column; }
    main { flex: 1; }
    .registro-card { max-width: 640px; }
  </style>
</head>
<body>
  <header class="top-header">
    <img src="img/logo_uta.png" alt="Logo UTA">
    <div class="site-name">Eventos Académicos FISEI</div>
  </header>

  <main class="card-custom registro-card">
    <h1 class="text-center">Crea una cuenta</h1>
    <p class="text-center text-muted mb-4">Es rápido y fácil.</p>

    <?php if (isset($error)): ?>
      <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="row mb-3">
        <div class="col">
          <input type="text" name="nombre" placeholder="Nombre" required>
        </div>
        <div class="col">
          <input type="text" name="apellido" placeholder="Apellido" required>
        </div>
      </div>

      <div class="mb-3">
        <input type="text" name="cedula" placeholder="Número de cédula" pattern="\d{10}" title="Debe contener 10 dígitos numéricos" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Fecha de nacimiento:</label>
        <div class="row">
          <div class="col">
            <select name="dia" required>
              <?php for ($d = 1; $d <= 31; $d++): ?>
                <option value="<?= $d ?>"><?= $d ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="col">
            <select name="mes" required>
              <?php
              $meses = ['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'];
              foreach ($meses as $num => $mes): ?>
                <option value="<?= $num ?>"><?= $mes ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col">
            <select name="anio" required>
              <?php for ($y = date('Y') - 10; $y >= 1960; $y--): ?>
                <option value="<?= $y ?>"><?= $y ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Género:</label><br>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="genero" value="Mujer" required>
          <label class="form-check-label">Mujer</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="genero" value="Hombre" required>
          <label class="form-check-label">Hombre</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="genero" value="Otro" required>
          <label class="form-check-label">Otro</label>
        </div>
      </div>

      <div class="mb-3">
        <input type="email" name="correo" placeholder="Correo electrónico" required>
      </div>

      <div class="mb-3">
        <input type="password" name="password" placeholder="Contraseña nueva" required>
      </div>

      <div class="text-center">
        <button type="submit" class="btn btn-primary">Registrarte</button>
      </div>
    </form>

    <div class="text-center mt-3">
      <a href="login.php" class="btn btn-outline-primary">¿Ya tienes una cuenta?</a>
    </div>
  </main>

  <footer class="footer-expandido">
    <div class="footer-container">
      <!-- contenido footer igual -->
    </div>
    <div class="footer-bottom">
      © <?= date('Y') ?> FISEI - Universidad Técnica de Ambato. Todos los derechos reservados.
    </div>
  </footer>
</body>
</html>
