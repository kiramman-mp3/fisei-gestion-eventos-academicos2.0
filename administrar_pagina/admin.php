<?php
include '../sql/conexion.php';
$conn = (new Conexion())->conectar();

$tipos = ['carrusel', 'nosotros', 'autoridad', 'resena'];
$datos = [];
foreach ($tipos as $tipo) {
  $stmt = $conn->prepare("SELECT id, contenido FROM info_fisei WHERE tipo = ?");
  $stmt->execute([$tipo]);
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $contenido = json_decode($row['contenido'], true);
    $contenido['id'] = $row['id'];
    $datos[$tipo][] = $contenido;
  }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Panel de Administración</title>
  <link rel="stylesheet" href="estilos.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
  <div id="toast" class="toast">
    <i class="fas fa-check-circle"></i>
    <span id="toast-text">Actualizado con éxito</span>
  </div>


  <header class="ctt-header">
    <div class="top-bar">
      <div class="logo">
        <img src="../resources/logo.png" alt="Logo FISEI">
      </div>
      <div class="top-links">
        <div class="link-box">
          <i class="fas fa-desktop"></i>
          <div>
            <span class="title">Plataforma Educativa</span><br>
            <a href="../index.php">Ingresa aquí</a>
          </div>
        </div>
      </div>
    </div>
    <nav class="main-nav">
      <div class="menu-icon">
        <i class="fas fa-bars"></i>
      </div>
      <ul class="menu">
        <li><a href="#">Inicio</a></li>
        <li><a href="#">Nosotros</a></li>
        <li><a href="#">Cursos</a></li>
        <li><a href="#">Contáctanos</a></li>
      </ul>
    </nav>
  </header>

  <div class="admin-panel">
    <h1>Panel de Administración</h1>

    <!-- SECCIÓN CARRUSEL -->
    <section class="admin-section">
      <h2>Carrusel</h2>
      <?php foreach ($datos['carrusel'] ?? [] as $c): ?>
        <form action="api_admin.php" method="post" enctype="multipart/form-data" class="admin-form">
          <input type="hidden" name="id" value="<?= $c['id'] ?>">
          <input type="hidden" name="tipo" value="carrusel">
          <input type="hidden" name="imagen_actual" value="<?= $c['img'] ?>">

          <div class="imagen-editable">
            <img src="<?= $c['img'] ?>" alt="Imagen actual">
            <label for="imgUpload<?= $c['id'] ?>" class="editar-icono">
              <i class="fas fa-pen"></i>
            </label>
            <input type="file" name="nueva_img" id="imgUpload<?= $c['id'] ?>" accept="image/*">
          </div>

          <div class="admin-form-fields">
            <input type="text" name="titulo" value="<?= $c['titulo'] ?>" required>
            <textarea name="descripcion" required><?= $c['descripcion'] ?></textarea>
            <button type="submit">Actualizar</button>
            <a href="api_admin.php?delete=<?= $c['id'] ?>"
              onclick="localStorage.setItem('toastMsg', 'eliminado')">Eliminar</a>
          </div>
        </form>
      <?php endforeach; ?>

      <h3>Nuevo Carrusel</h3>
      <form action="api_admin.php" method="post" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="tipo" value="carrusel">
        <div class="imagen-editable">
          <img src="" alt="Vista previa" style="display: none;">
          <label for="imgNuevoCarrusel" class="editar-icono">
            <i class="fas fa-plus"></i>
          </label>
          <input type="file" name="nueva_img" id="imgNuevoCarrusel" accept="image/*" required>
        </div>
        <div class="admin-form-fields">
          <input type="text" name="titulo" placeholder="Título" required>
          <textarea name="descripcion" placeholder="Descripción" required></textarea>
          <button type="submit" onclick="localStorage.setItem('toastMsg', 'creado')">Agregar</button>
        </div>
      </form>
    </section>

    <!-- SECCIÓN NOSOTROS -->
    <section class="admin-section">
      <h2>Nosotros</h2>
      <?php foreach ($datos['nosotros'] ?? [] as $n): ?>
        <form action="api_admin.php" method="post" enctype="multipart/form-data" class="admin-form">
          <input type="hidden" name="id" value="<?= $n['id'] ?>">
          <input type="hidden" name="tipo" value="nosotros">
          <input type="hidden" name="imagen_actual" value="<?= $n['img'] ?>">

          <div class="imagen-editable">
            <img src="<?= $n['img'] ?>" alt="Imagen actual">
            <label for="imgNosotros<?= $n['id'] ?>" class="editar-icono">
              <i class="fas fa-pen"></i>
            </label>
            <input type="file" name="nueva_img" id="imgNosotros<?= $n['id'] ?>" accept="image/*">
          </div>

          <div class="admin-form-fields">
            <input type="text" name="titulo" value="<?= $n['titulo'] ?>" required>
            <textarea name="descripcion" required><?= $n['descripcion'] ?></textarea>
            <button type="submit">Actualizar</button>
          </div>
        </form>
      <?php endforeach; ?>
    </section>

    <!-- SECCIÓN AUTORIDADES -->
    <section class="admin-section">
      <h2>Autoridades</h2>
      <?php foreach ($datos['autoridad'] ?? [] as $a): ?>
        <form action="api_admin.php" method="post" enctype="multipart/form-data" class="admin-form">
          <input type="hidden" name="id" value="<?= $a['id'] ?>">
          <input type="hidden" name="tipo" value="autoridad">
          <input type="hidden" name="imagen_actual" value="<?= $a['img'] ?>">

          <div class="imagen-editable">
            <img src="<?= $a['img'] ?>" alt="Imagen autoridad">
            <label for="imgAutoridad<?= $a['id'] ?>" class="editar-icono">
              <i class="fas fa-pen"></i>
            </label>
            <input type="file" name="nueva_img" id="imgAutoridad<?= $a['id'] ?>" accept="image/*">
          </div>

          <div class="admin-form-fields">
            <input type="text" name="nombre" value="<?= $a['nombre'] ?>" required>
            <input type="text" name="cargo" value="<?= $a['cargo'] ?>" required>
            <button type="submit">Actualizar</button>
            <a href="api_admin.php?delete=<?= $a['id'] ?>"
              onclick="localStorage.setItem('toastMsg', 'eliminado')">Eliminar</a>
          </div>
        </form>
      <?php endforeach; ?>

      <h3>Nueva Autoridad</h3>
      <form action="api_admin.php" method="post" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="tipo" value="autoridad">
        <div class="imagen-editable">
          <img src="" alt="Vista previa" style="display: none;">
          <label for="imgNuevoAutoridad" class="editar-icono">
            <i class="fas fa-plus"></i>
          </label>
          <input type="file" name="nueva_img" id="imgNuevoAutoridad" accept="image/*" required>
        </div>
        <div class="admin-form-fields">
          <input type="text" name="nombre" placeholder="Nombre" required>
          <input type="text" name="cargo" placeholder="Cargo" required>
          <button type="submit" onclick="localStorage.setItem('toastMsg', 'creado')">Agregar</button>
        </div>
      </form>

    </section>

    <!-- SECCIÓN RESEÑAS -->
    <section class="admin-section">
      <h2>Reseñas</h2>
      <?php foreach ($datos['resena'] ?? [] as $r): ?>
        <form action="api_admin.php" method="post" enctype="multipart/form-data" class="admin-form">
          <input type="hidden" name="id" value="<?= $r['id'] ?>">
          <input type="hidden" name="tipo" value="resena">
          <input type="hidden" name="imagen_actual" value="<?= $r['img'] ?>">

          <div class="imagen-editable">
            <img src="<?= $r['img'] ?>" alt="Imagen reseña">
            <label for="imgResena<?= $r['id'] ?>" class="editar-icono">
              <i class="fas fa-pen"></i>
            </label>
            <input type="file" name="nueva_img" id="imgResena<?= $r['id'] ?>" accept="image/*">
          </div>

          <div class="admin-form-fields">
            <input type="text" name="autor" value="<?= $r['autor'] ?>" required>
            <input type="text" name="rol" value="<?= $r['rol'] ?>" required>
            <textarea name="texto" required><?= $r['texto'] ?></textarea>
            <button type="submit">Actualizar</button>
            <a href="api_admin.php?delete=<?= $r['id'] ?>"
              onclick="localStorage.setItem('toastMsg', 'eliminado')">Eliminar</a>
          </div>
        </form>
      <?php endforeach; ?>

      <h3>Nueva Reseña</h3>
      <form action="api_admin.php" method="post" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="tipo" value="resena">
        <div class="imagen-editable">
          <img src="" alt="Vista previa" style="display: none;">
          <label for="imgNuevoResena" class="editar-icono">
            <i class="fas fa-plus"></i>
          </label>
          <input type="file" name="nueva_img" id="imgNuevoResena" accept="image/*" required>
        </div>
        <div class="admin-form-fields">
          <input type="text" name="autor" placeholder="Autor" required>
          <input type="text" name="rol" placeholder="Rol" required>
          <textarea name="texto" placeholder="Texto" required></textarea>
          <button type="submit" onclick="localStorage.setItem('toastMsg', 'creado')">Agregar</button>
        </div>
      </form>
    </section>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const showToast = (mensaje) => {
        const toast = document.getElementById("toast");
        const toastText = document.getElementById("toast-text");
        if (toast && toastText) {
          toastText.textContent = mensaje;
          toast.classList.add("show");
          setTimeout(() => {
            toast.classList.remove("show");
          }, 3000);
        }
      };

      // Vista previa de imagen seleccionada
      const fileInputs = document.querySelectorAll('input[type="file"][name="nueva_img"]');
      fileInputs.forEach(input => {
        input.addEventListener('change', (e) => {
          const file = e.target.files[0];
          if (file && file.type.startsWith('image/')) {
            const previewImg = input.closest('.imagen-editable').querySelector('img');
            previewImg.src = URL.createObjectURL(file);
            previewImg.style.display = 'block';
          }
        });
      });

      // Guardar "actualizado" antes del submit
      const forms = document.querySelectorAll('form.admin-form');
      forms.forEach(form => {
        form.addEventListener('submit', () => {
          localStorage.setItem('toastMsg', 'actualizado');
        });
      });

      // Mostrar el toast después de la recarga según el mensaje
      const toastMsg = localStorage.getItem('toastMsg');
      if (toastMsg === 'actualizado') {
        showToast("Actualizado con éxito");
        localStorage.removeItem('toastMsg');
      } else if (toastMsg === 'eliminado') {
        showToast("Eliminado con éxito");
        localStorage.removeItem('toastMsg');
      }
    });
  </script>


</body>

</html>