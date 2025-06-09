<?php
require_once '../sql/conexion.php';
require_once '../session.php';

$cris = new Conexion();
$conexion = $cris->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = $_POST['nombre'];
  $apellido = $_POST['apellido'];
  $correo = $_POST['correo'];
  $cedula = $_POST['cedula'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $genero = $_POST['genero'];
  $rol = $_POST['rol'];
  $fecha_nacimiento = $_POST['anio'] . '-' . $_POST['mes'] . '-' . $_POST['dia'];

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
        (nombre, apellido, correo, password, cedula, genero, fecha_nacimiento, tipo, rol)
        VALUES (:nombre, :apellido, :correo, :password, :cedula, :genero, :fecha_nacimiento, :tipo, :rol)");

      $stmt->bindValue(':nombre', $nombre);
      $stmt->bindValue(':apellido', $apellido);
      $stmt->bindValue(':correo', $correo);
      $stmt->bindValue(':password', $password);
      $stmt->bindValue(':cedula', $cedula);
      $stmt->bindValue(':genero', $genero);
      $stmt->bindValue(':fecha_nacimiento', $fecha_nacimiento);
      $stmt->bindValue(':tipo', $tipo);
      $stmt->bindValue(':rol', $rol);

      if ($stmt->execute()) {
        header("Location: ../dashboard.html?mensaje=Admin creado con éxito");
        exit;
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
  <title>Registrar Nuevo Administrador</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/styles.css?v=1">
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
        <a href="../perfil.php" class="fw-semibold text-white text-decoration-none">
  Hola, <?= htmlspecialchars(getUserName()) ?> <?= htmlspecialchars(getUserLastname()) ?>
</a>

        <a href="../logout.php" class="btn btn-white"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
      <?php else: ?>
        <a href="../login.php" class="btn btn-white"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
        <a href="../registro.php" class="btn btn-white"><i class="fas fa-user-plus"></i> Registrarse</a>
      <?php endif; ?>
    </div>
  </header>

  <main class="card-custom registro-card">
    <h1 class="text-center">Agregar nuevo administrador</h1>
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
        <input type="text" name="cedula" placeholder="Cédula" pattern="\d{10}" required>
      </div>

      <div class="row mb-3">
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

      <div class="mb-3">
        <label class="form-label">Género:</label><br>
        <input type="radio" name="genero" value="Mujer" required> Mujer
        <input type="radio" name="genero" value="Hombre" required> Hombre
        <input type="radio" name="genero" value="Otro" required> Otro
      </div>

      <div class="mb-3">
        <input type="email" name="correo" placeholder="Correo" required>
      </div>

      <div class="mb-3">
        <input type="password" name="password" placeholder="Contraseña" required>
      </div>

      <div class="mb-3">
        <label for="rol">Rol del usuario:</label>
        <select name="rol" class="form-select" required>
          <option value="administrador">Administrador</option>
        </select>
      </div>

      <div class="text-center">
        <button type="submit" class="btn btn-primary">Crear administrador</button>
      </div>
    </form>

    <div class="text-center mt-3">
      <a href="../dashboard.html" class="btn btn-secondary">Volver al dashboard</a>
    </div>
  </main>
</body>
</html>
