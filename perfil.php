<?php
require_once 'session.php';
include('sql/conexion.php');

if (!isLoggedIn()) {
  header('Location: login.php');
  exit;
}

$nombre = getUserName();
  $apellido = getUserLastname();

$usuarioId = getUserId();

$cris = new Conexion();
$conexion = $cris->conectar();
$stmt = $conexion->prepare("SELECT * FROM estudiantes WHERE id = ?");
$stmt->bindValue(1, $usuarioId, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
  echo "Usuario no encontrado.";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $carrera = $usuario['tipo'] === 'institucional' && empty($usuario['carrera']) ? ($_POST['carrera'] ?? null) : $usuario['carrera'];
  $correo = $usuario['correo'];
  $uploadDir = 'uploads/documentos/' . str_replace('@', '_', $correo) . '/';
  if (!file_exists($uploadDir))
    mkdir($uploadDir, 0777, true);

  $rutas = [
    'cedula_path' => $usuario['cedula_path'],
    'papeleta_path' => $usuario['papeleta_path'],
    'matricula_path' => $usuario['matricula_path']
  ];

  foreach ($rutas as $key => &$ruta) {
    $campo = str_replace('_path', '', $key);
    if (isset($_FILES[$campo]) && $_FILES[$campo]['error'] === 0) {
      $file = $_FILES[$campo];
      $destino = $uploadDir . $campo . '_' . basename($file['name']);
      if (move_uploaded_file($file['tmp_name'], $destino)) {
        $ruta = $destino;
      }
    }
  }

  $update = $conexion->prepare("
    UPDATE estudiantes SET 
        carrera = ?, 
        cedula_path = ?, 
        papeleta_path = ?, 
        matricula_path = ?
    WHERE id = ?
  ");
  $update->bindValue(1, $carrera);
  $update->bindValue(2, $rutas['cedula_path']);
  $update->bindValue(3, $rutas['papeleta_path']);
  $update->bindValue(4, $rutas['matricula_path']);
  $update->bindValue(5, $usuarioId);
  $update->execute();

  header("Location: perfil.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mi Perfil</title>
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
<header class="top-header d-flex justify-content-between align-items-center px-4 py-2 shadow-sm --maroon">
    <div class="d-flex align-items-center">
    <a href="index.php">
  <img src="resource/logo-universidad-tecnica-de-ambato.webp" alt="Logo institucional" style="height: 50px;">
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

  <main>
    <div class="card">
      <h1><i class="fas fa-user"></i> Mi Perfil</h1>

      <div style="line-height: 1.9;">
        <p><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></p>
        <p><strong>Cédula:</strong> <?= htmlspecialchars($usuario['cedula']) ?></p>
        <p><strong>Correo:</strong> <?= htmlspecialchars($usuario['correo']) ?></p>
        <p><strong>Tipo:</strong> <?= $usuario['tipo'] === 'institucional' ? 'Universitario UTA' : 'Público' ?></p>
        <p><strong>Género:</strong> <?= htmlspecialchars($usuario['genero']) ?></p>
        <p><strong>Fecha de nacimiento:</strong> <?= htmlspecialchars($usuario['fecha_nacimiento']) ?></p>
      </div>

      <form method="POST" enctype="multipart/form-data" style="margin-top: 2rem;">
        <?php if ($usuario['tipo'] === 'institucional' && empty($usuario['carrera'])): ?>
          <div class="mb-3">
            <label for="carrera"><i class="fas fa-graduation-cap"></i><strong>Carrera:</strong></label>
            <select name="carrera" id="carrera" required>
              <option value="">--Seleccione--</option>
              <option value="Software">Ingeniería en Software</option>
              <option value="Electrónica">Ingeniería Electrónica</option>
              <option value="Industrial">Ingeniería Industrial</option>
            </select>
          </div>
        <?php elseif ($usuario['tipo'] === 'publico'): ?>
          <p class="alert alert-warning" style="color: var(--gray-600); margin-top: 12px;">
            Tu correo es público. No puedes seleccionar una carrera.
          </p>
          <input type="hidden" name="carrera" value="">
        <?php else: ?>
          <p><strong><i class="fas fa-graduation-cap"></i> Carrera:</strong>
            <?= htmlspecialchars($usuario['carrera']) ?: 'No asignada' ?></p>
          <input type="hidden" name="carrera" value="<?= htmlspecialchars($usuario['carrera']) ?>">
        <?php endif; ?>

        <fieldset style="margin-top: 2rem;">
          <legend><i class="fas fa-folder-open"></i> Documentación</legend>

          <?php
          $docs = [
            'cedula' => 'Cédula escaneada',
            'papeleta' => 'Papeleta de votación',
            'matricula' => 'Matrícula'
          ];
          foreach ($docs as $campo => $etiqueta):
            $path = $usuario[$campo . '_path'];
            $previewId = "preview-$campo";
            ?>
            <div class="mb-3">
              <label for="<?= $campo ?>"><i class="fas fa-upload"></i><?= $etiqueta ?>:</label>
              <input type="file" name="<?= $campo ?>" id="<?= $campo ?>" accept="application/pdf,image/*"
                onchange="previewPDF(this, '<?= $previewId ?>')">
              <?php if (!empty($path)): ?>
                <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $path)): ?>
                  <img src="<?= $path ?>" class="thumbnail">
                <?php elseif (preg_match('/\.pdf$/i', $path)): ?>
                  <embed src="<?= $path ?>" type="application/pdf" width="50%" height="400px" class="thumbnail">
                <?php else: ?>
                  <p style="color: red;">Archivo no soportado.</p>
                <?php endif; ?>
              <?php endif; ?>
              <div id="<?= $previewId ?>"></div>
            </div>
          <?php endforeach; ?>
        </fieldset>

        <div class="actions" style="display: flex; justify-content: space-between; margin-top: 2.5rem;">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar cambios</button>
          <a href="logout.php" class="btn btn-outline-secondary"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
        </div>

        <div class="text-center" style="margin-top: 2rem;">
          <a href="estudiantes/mis_cursos.php" class="btn btn-outline-primary"><i class="fas fa-book-open"></i> Ver mis cursos</a>
        </div>
      </form>
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


  <script>
    function previewPDF(input, previewId) {
      const file = input.files[0];
      const preview = document.getElementById(previewId);
      preview.innerHTML = '';
      if (!file) return;

      if (file.type.includes('image')) {
        const reader = new FileReader();
        reader.onload = () => {
          preview.innerHTML = `<img src="${reader.result}" class="thumbnail">`;
        };
        reader.readAsDataURL(file);
      } else if (file.type === 'application/pdf') {
        const url = URL.createObjectURL(file);
        preview.innerHTML = `<embed src="${url}" type="application/pdf" width="50%" height="400px" class="thumbnail">`;
      } else {
        preview.innerHTML = `<p style="color: red; margin-top: 8px;">Formato no soportado para vista previa.</p>`;
      }
    }
  </script>
</body>

</html>