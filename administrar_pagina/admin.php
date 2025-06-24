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
  <link rel="stylesheet" href="../css/panel-estilos.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
  <div id="toast" class="toast"><i class="fas fa-check-circle"></i><span id="toast-text">Actualizado con éxito</span>
  </div>

  <header class="ctt-header">
    <div class="top-bar">
      <div class="logo"><img src="../uploads/logo.png" alt="Logo FISEI"></div>
      <div class="top-links">
        <div class="link-box">
          <i class="fa-solid fa-arrow-left"></i>
          <div>
            <span class="title">Regresar</span><br>
            <a href="javascript:history.back()">Regrega al Dashboard</a>
          </div>
        </div>
      </div>
    </div>
  </header>

  <main class="admin-panel">
    <h1 class="titulo-formulario"><i class="fas fa-sliders-h"></i> Panel de Administración</h1>

    <!-- Pestañas -->
    <ul class="pestanas">
      <li><button class="active" data-tab="carrusel"><i class="fas fa-images"></i> Carrusel</button></li>
      <li><button data-tab="nosotros"><i class="fas fa-users"></i> Nosotros</button></li>
      <li><button data-tab="autoridades"><i class="fas fa-user-tie"></i> Autoridades</button></li>
      <li><button data-tab="resenas"><i class="fas fa-comment-dots"></i> Reseñas</button></li>
    </ul>

    <!-- Contenedor general para las secciones -->
    <div class="contenedor-secciones">
      <!-- CARRUSEL -->
      <section id="carrusel" class="tab-content active">
        <div class="admin-section">
          <h2>Carrusel</h2>
          <?php foreach ($datos['carrusel'] ?? [] as $c): ?>
            <form action="api_admin.php" method="post" enctype="multipart/form-data" class="admin-form">
              <input type="hidden" name="id" value="<?= $c['id'] ?>">
              <input type="hidden" name="tipo" value="carrusel">
              <input type="hidden" name="imagen_actual" value="<?= $c['img'] ?>">
              <div class="imagen-editable">
                <img src="<?= '../' . $c['img'] ?>" alt="">
                <label for="imgUpload<?= $c['id'] ?>" class="editar-icono"><i class="fas fa-pen"></i></label>
                <input type="file" name="nueva_img" id="imgUpload<?= $c['id'] ?>" accept="image/*">
              </div>
              <div class="admin-form-fields">
                <input type="text" name="titulo" value="<?= $c['titulo'] ?>" required>
                <textarea name="descripcion" required><?= $c['descripcion'] ?></textarea>
                <button type="submit">Actualizar</button>
                <a href="api_admin.php?delete=<?= $c['id'] ?>"
                  onclick="localStorage.setItem('toastMsg','eliminado')">Eliminar</a>
              </div>
            </form>
          <?php endforeach; ?>

          <h3>Nuevo Carrusel</h3>
          <form action="api_admin.php" method="post" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="tipo" value="carrusel">
            <div class="imagen-editable">
              <img src="" style="display:none;">
              <label for="imgNuevoCarrusel" class="editar-icono"><i class="fas fa-plus"></i></label>
              <input type="file" name="nueva_img" id="imgNuevoCarrusel" accept="image/*" required>
            </div>
            <div class="admin-form-fields">
              <input type="text" name="titulo" placeholder="Título" required>
              <textarea name="descripcion" placeholder="Descripción" required></textarea>
              <button type="submit" onclick="localStorage.setItem('toastMsg','creado')">Agregar</button>
            </div>
          </form>
        </div>
      </section>

      <!-- NOSOTROS -->
      <section id="nosotros" class="tab-content">
        <div class="admin-section">
          <h2>Nosotros</h2>
          <?php foreach ($datos['nosotros'] ?? [] as $n): ?>
            <form action="api_admin.php" method="post" enctype="multipart/form-data" class="admin-form">
              <input type="hidden" name="id" value="<?= $n['id'] ?>">
              <input type="hidden" name="tipo" value="nosotros">
              <input type="hidden" name="imagen_actual" value="<?= $n['img'] ?>">
              <div class="imagen-editable">
                <img src="<?= '../' . $n['img'] ?>" alt="">
                <label for="imgNosotros<?= $n['id'] ?>" class="editar-icono"><i class="fas fa-pen"></i></label>
                <input type="file" name="nueva_img" id="imgNosotros<?= $n['id'] ?>" accept="image/*">
              </div>
              <div class="admin-form-fields">
                <input type="text" name="titulo" value="<?= $n['titulo'] ?>" required>
                <textarea name="descripcion" required><?= $n['descripcion'] ?></textarea>
                <button type="submit">Actualizar</button>
              </div>
            </form>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- AUTORIDADES -->
      <section id="autoridades" class="tab-content">
        <div class="admin-section">
          <h2>Autoridades</h2>
          <?php foreach ($datos['autoridad'] ?? [] as $a): ?>
            <form action="api_admin.php" method="post" enctype="multipart/form-data" class="admin-form">
              <input type="hidden" name="id" value="<?= $a['id'] ?>">
              <input type="hidden" name="tipo" value="autoridad">
              <input type="hidden" name="imagen_actual" value="<?= $a['img'] ?>">
              <div class="imagen-editable">
                <img src="<?= '../' . $a['img'] ?>" alt="">
                <label for="imgAutoridad<?= $a['id'] ?>" class="editar-icono"><i class="fas fa-pen"></i></label>
                <input type="file" name="nueva_img" id="imgAutoridad<?= $a['id'] ?>" accept="image/*">
              </div>
              <div class="admin-form-fields">
                <input type="text" name="nombre" value="<?= $a['nombre'] ?>" required>
                <input type="text" name="cargo" value="<?= $a['cargo'] ?>" required>
                <button type="submit">Actualizar</button>
                <a href="api_admin.php?delete=<?= $a['id'] ?>"
                  onclick="localStorage.setItem('toastMsg','eliminado')">Eliminar</a>
              </div>
            </form>
          <?php endforeach; ?>

          <!-- NUEVA AUTORIDAD -->
          <h3>Nueva Autoridad</h3>
          <form action="api_admin.php" method="post" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="tipo" value="autoridad">
            <div class="imagen-editable">
              <img src="" style="display:none;">
              <label for="imgNuevoAutoridad" class="editar-icono"><i class="fas fa-plus"></i></label>
              <input type="file" name="nueva_img" id="imgNuevoAutoridad" accept="image/*" required>
            </div>
            <div class="admin-form-fields">
              <input type="text" name="nombre" placeholder="Nombre completo" required>
              <input type="text" name="cargo" placeholder="Cargo" required>
              <button type="submit" onclick="localStorage.setItem('toastMsg','creado')">Agregar</button>
            </div>
          </form>
        </div>
      </section>

      <!-- RESEÑAS -->
      <!-- RESEÑAS -->
      <section id="resenas" class="tab-content">
        <div class="admin-section">
          <h2>Reseñas</h2>
          <?php foreach ($datos['resena'] ?? [] as $r): ?>
            <form action="api_admin.php" method="post" enctype="multipart/form-data" class="admin-form">
              <input type="hidden" name="id" value="<?= $r['id'] ?>">
              <input type="hidden" name="tipo" value="resena">
              <input type="hidden" name="imagen_actual" value="<?= $r['img'] ?>">
              <div class="imagen-editable">
                <img src="<?= '../' . $r['img'] ?>" alt="">
                <label for="imgResena<?= $r['id'] ?>" class="editar-icono"><i class="fas fa-pen"></i></label>
                <input type="file" name="nueva_img" id="imgResena<?= $r['id'] ?>" accept="image/*">
              </div>
              <div class="admin-form-fields">
                <input type="text" name="autor" value="<?= $r['autor'] ?>" required>
                <input type="text" name="rol" value="<?= $r['rol'] ?>" required>
                <textarea name="texto" required><?= $r['texto'] ?></textarea>
                <button type="submit">Actualizar</button>
                <a href="api_admin.php?delete=<?= $r['id'] ?>"
                  onclick="localStorage.setItem('toastMsg','eliminado')">Eliminar</a>
              </div>
            </form>
          <?php endforeach; ?>

          <!-- NUEVA RESEÑA -->
          <h3>Nueva Reseña</h3>
          <form action="api_admin.php" method="post" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="tipo" value="resena">
            <div class="imagen-editable">
              <img src="" alt="Vista previa" style="display: none;">
              <label for="imgNuevaResena" class="editar-icono"><i class="fas fa-plus"></i></label>
              <input type="file" name="nueva_img" id="imgNuevaResena" accept="image/*" required>
            </div>
            <div class="admin-form-fields">
              <input type="text" name="autor" placeholder="Autor de la reseña" required>
              <input type="text" name="rol" placeholder="Rol o cargo" required>
              <textarea name="texto" placeholder="Escribe la reseña aquí..." required></textarea>
              <button type="submit" onclick="localStorage.setItem('toastMsg','creado')">Agregar</button>
            </div>
          </form>
        </div>
      </section>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const tabs = document.querySelectorAll('.pestanas button');
      const contents = document.querySelectorAll('.tab-content');
      tabs.forEach(tab => {
        tab.addEventListener('click', () => {
          tabs.forEach(t => t.classList.remove('active'));
          contents.forEach(c => c.classList.remove('active'));
          tab.classList.add('active');
          document.getElementById(tab.dataset.tab).classList.add('active');
        });
      });

      const showToast = msg => {
        const toast = document.getElementById('toast');
        const toastText = document.getElementById('toast-text');
        toastText.textContent = msg;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
      };

      document.querySelectorAll('input[type="file"][name="nueva_img"]').forEach(input => {
        input.addEventListener('change', e => {
          const f = e.target.files[0];
          if (f && f.type.startsWith('image/')) {
            const img = input.closest('.imagen-editable').querySelector('img');
            img.src = URL.createObjectURL(f);
            img.style.display = 'block';
          }
        });
      });

      document.querySelectorAll('form.admin-form').forEach(f => {
        f.addEventListener('submit', () => localStorage.setItem('toastMsg', 'actualizado'));
      });

      const t = localStorage.getItem('toastMsg');
      if (t) {
        showToast(t === 'creado' ? 'Elemento creado' : t === 'eliminado' ? 'Eliminado con éxito' : 'Actualizado con éxito');
        localStorage.removeItem('toastMsg');
      }
    });
  </script>
</body>

</html>