<?php
require_once __DIR__ . '/../session.php';

if (!function_exists('isLoggedIn')) {
    die("Error: No se pudo cargar correctamente session.php");
}

$nombre = getUserName();
$apellido = getUserLastname();

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Cursos</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .parent {
            display: grid;
            grid-template-columns: 1fr;
            grid-template-rows: auto auto auto auto auto auto auto auto;
            gap: 30px;
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header,
        .header2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--maroon-dark);
            border-bottom: 2px solid var(--maroon);
            padding-bottom: 0.5rem;
        }

        .div-form-categoria,
        .div-form-tipo,
        .div-form-curso {
            background: #fff;
            border-radius: 14px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        table {
            margin-top: 1rem;
        }
    </style>
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
        Hola,
        <?= htmlspecialchars(getUserName()) ?>
        <?= htmlspecialchars(getUserLastname()) ?>
      </a>
  
      <a href="../logout.php" class="btn btn-white"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
      <?php else: ?>
      <a href="../login.php" class="btn btn-white"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
      <a href="../registro.php" class="btn btn-white"><i class="fas fa-user-plus"></i> Registrarse</a>
      <?php endif; ?>
    </div>
  </header>

    <div class="parent">
        <div class="header">Gestión de Categorías y Tipos</div>

        <!-- FORMULARIO CATEGORÍA -->
        <div class="div-form-categoria">
            <form id="formCategoria">
                <input type="hidden" name="id" id="cat-id">
                <label for="cat-nombre"><span class="rojo">*</span> Nombre de la categoría</label>
                <input type="text" name="nombre" id="cat-nombre" placeholder="Nombre de la categoría" required>
                <div class="actions">
                    <button type="submit" class="btn btn-primary">Guardar Categoría</button>
                </div>
            </form>
        </div>

        <!-- TABLA CATEGORÍAS -->
        <div class="div-tabla-categoria">
            <h2>Categorías Existentes</h2>
            <table class="table-custom" id="tablaCategorias">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- FORMULARIO TIPO EVENTO -->
        <div class="div-form-tipo">
            <form id="formTipo">
                <input type="hidden" name="id" id="tipo-id">
                <label for="tipo-nombre"><span class="rojo">*</span> Tipo de evento</label>
                <input type="text" name="nombre" id="tipo-nombre" placeholder="Tipo de evento (curso, evento...)"
                    required>
                <div class="actions">
                    <button type="submit" class="btn btn-primary">Guardar Tipo</button>
                </div>
            </form>
        </div>

        <!-- TABLA TIPOS DE EVENTO -->
        <div class="div-tabla-tipo">
            <h2>Tipos de Evento Existentes</h2>
            <table class="table-custom" id="tablaTipos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="header2">Gestión de Cursos</div>

        <!-- FORMULARIO CURSO -->
        <div class="div-form-curso">
            <form id="formCurso" enctype="multipart/form-data">
                <input type="hidden" name="id" id="curso-id">

                <label><span class="rojo">*</span> Nombre del curso</label>
                <input type="text" name="nombre_evento" required placeholder="Nombre del curso">

                <label for="selectTipo"><span class="rojo">*</span> Tipo de evento</label>
                <select name="tipo_evento_id" required id="selectTipo">
                    <option value="">Seleccionar tipo de evento</option>
                </select>

                <label for="selectCategoria"><span class="rojo">*</span> Categoría</label>
                <select name="categoria_id" required id="selectCategoria">
                    <option value="">Seleccionar categoría</option>
                </select>

                <label><span class="rojo">*</span> Ponentes</label>
                <input type="text" name="ponentes" required placeholder="Ponentes">

                <label>Fecha de inicio</label>
                <input type="date" name="fecha_inicio" required>

                <label>Fecha de fin</label>
                <input type="date" name="fecha_fin" required>

                <label>Inicio de inscripciones</label>
                <input type="date" name="fecha_inicio_inscripciones" required>

                <label>Fin de inscripciones</label>
                <input type="date" name="fecha_fin_inscripciones" required>

                <label>Horas</label>
                <input type="number" name="horas" placeholder="Horas" required>

                <label>Cupos</label>
                <input type="number" name="cupos" placeholder="Cupos" required>

                <label>Imagen del curso</label>
                <input type="file" name="imagen" accept="image/*" required>

                <label>Estado</label>
                <select name="estado" required>
                    <option value="abierto">Abierto</option>
                    <option value="cerrado">Cerrado</option>
                    <option value="en ejecucion">En ejecución</option>
                    <option value="cerrado para inscripciones">Cerrado para inscripciones</option>
                </select>

                <div class="actions">
                    <button type="submit" class="btn btn-primary">Guardar Curso</button>
                </div>
            </form>
        </div>

        <!-- TABLA CURSOS -->
        <div class="div-tabla-curso">
            <h2>Cursos Existentes</h2>
            <table class="table-custom" id="tablaCursos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Categoría</th>
                        <th>Ponentes</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Inicio Inscripción</th>
                        <th>Fin Inscripción</th>
                        <th>Horas</th>
                        <th>Cupos</th>
                        <th>Estado</th>
                        <th>Imagen</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

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
    ©  FISEI - Universidad Técnica de Ambato. Todos los derechos reservados.
  </div>
</footer>



    <script>
        const tabla = document.querySelector("#tablaCursos tbody");

        async function cargarCursos() {
            try {
                const res = await fetch("../service/curso.php?action=listar");
                const cursos = await res.json();
                tabla.innerHTML = cursos.map(c => `
                <tr>
                    <td>${c.id}</td>
                    <td>${c.nombre_evento}</td>
                    <td>${c.tipo_nombre}</td>
                    <td>${c.categoria_nombre}</td>
                    <td>${c.ponentes}</td>
                    <td>${c.fecha_inicio}</td>
                    <td>${c.fecha_fin}</td>
                    <td>${c.fecha_inicio_inscripciones}</td>
                    <td>${c.fecha_fin_inscripciones}</td>
                    <td>${c.horas}</td>
                    <td>${c.cupos}</td>
                    <td>${c.estado}</td>
                    <td><img src="${c.ruta_imagen}" alt="imagen" width="80"></td>
                </tr>
            `).join("");
            } catch (e) {
                alert("Error al cargar cursos.");
            }
        }

        document.getElementById("formCurso").addEventListener("submit", async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const url = "../service/curso.php?action=" + (formData.get("id") ? "editar" : "crear");

            try {
                const res = await fetch(url, {
                    method: "POST",
                    body: formData
                });
                const text = await res.text();
                console.log("Respuesta:", text);
                const result = JSON.parse(text);

                if (result.success) {
                    alert("Curso guardado correctamente");
                    cargarCursos();
                    e.target.reset();
                } else {
                    alert("Error al guardar curso.");
                }
            } catch (err) {
                console.error("Error:", err);
                alert("Fallo en la conexión al guardar curso.");
            }
        });

        document.getElementById("formCategoria").addEventListener("submit", async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const url = "../service/categoria_evento.php?action=" + (formData.get("id") ? "editar" : "crear");

            try {
                const res = await fetch(url, {
                    method: "POST",
                    body: formData
                });
                const result = await res.json();
                alert(result.mensaje);
                location.reload();
            } catch {
                alert("Error al guardar categoría.");
            }
        });

        document.getElementById("formTipo").addEventListener("submit", async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const url = "../service/tipo_evento.php?action=" + (formData.get("id") ? "editar" : "crear");

            try {
                const res = await fetch(url, {
                    method: "POST",
                    body: formData
                });
                const result = await res.json();
                alert(result.mensaje);
                location.reload();
            } catch {
                alert("Error al guardar tipo de evento.");
            }
        });

        async function cargarSelects() {
            try {
                const [tipos, categorias] = await Promise.all([
                    fetch("../service/tipo_evento.php?action=listar").then(r => r.json()),
                    fetch("../service/categoria_evento.php?action=listar").then(r => r.json())
                ]);

                const selTipo = document.getElementById("selectTipo");
                selTipo.innerHTML = '<option value="">Seleccionar tipo de evento</option>';
                tipos.forEach(t => {
                    selTipo.innerHTML += `<option value="${t.id}">${t.nombre}</option>`;
                });

                const selCat = document.getElementById("selectCategoria");
                selCat.innerHTML = '<option value="">Seleccionar categoría</option>';
                categorias.forEach(c => {
                    selCat.innerHTML += `<option value="${c.id}">${c.nombre}</option>`;
                });

                const tablaCat = document.querySelector("#tablaCategorias tbody");
                if (tablaCat) {
                    tablaCat.innerHTML = categorias.map(c => `
                    <tr>
                        <td>${c.id}</td>
                        <td>${c.nombre}</td>
                    </tr>
                `).join("");
                }

                const tablaTipos = document.querySelector("#tablaTipos tbody");
                if (tablaTipos) {
                    tablaTipos.innerHTML = tipos.map(t => `
                    <tr>
                        <td>${t.id}</td>
                        <td>${t.nombre}</td>
                    </tr>
                `).join("");
                }
            } catch (e) {
                console.error(e);
                alert("Error al cargar selectores.");
            }
        }

        cargarSelects();
        cargarCursos();
    </script>Add commentMore actions
</body>

</html> 