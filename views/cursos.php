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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/gestion-css.css">

<body>
    <header class="ctt-header">
        <div class="top-bar">
            <div class="logo">
                <img src="../uploads/logo.png" alt="Logo CTT">
            </div>
            <div class="top-links">
                <div class="link-box">
                    <i class="fa-solid fa-arrow-left"></i>
                    <div>
                        <span class="title">Regresar</span>
                        <br>
                        <a href="javascript:history.back()">Página anterior</a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <ul class="pestanas">
        <li><button class="tab-btn active" data-tab="tab-categorias"><i
                    class="fas fa-layer-group"></i>Categorías</button></li>
        <li><button class="tab-btn" data-tab="tab-tipos"><i class="fas fa-tags"></i>Tipos de Evento</button></li>
    </ul>
    <div class="contenedor-secciones">
        <div id="tab-categorias" class="tab-content active">
            <div class="admin-section">
                <h2>Categorías</h2>
                <form id="formCategoria" class="admin-form">
                    <div class="admin-form-fields">
                        <div>
                            <input type="hidden" name="id" id="cat-id">
                            <label for="cat-nombre"><span class="rojo">*</span>Nombre de la categoría</label>
                            <input type="text" name="nombre" id="cat-nombre" required>
                        </div>
                        <div>
                            <label for="cat-requiere-nota">
                                <input type="checkbox" name="requiere_nota" id="cat-requiere-nota" value="1">
                                Requiere calificación
                            </label>
                        </div>
                        <div>
                            <label for="cat-requiere-asistencia">
                                <input type="checkbox" name="requiere_asistencia" id="cat-requiere-asistencia" value="1">
                                Requiere control de asistencia
                            </label>
                        </div>
                        <div class="text-muted small">
                            <i class="fas fa-info-circle"></i> Debe seleccionar al menos una opción (calificación o asistencia)
                        </div>
                        <button type="submit">Guardar Categoría</button>
                    </div>
                </form>
                <div>
                    <h3>Categorías Existentes</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Req. Nota</th>
                                    <th>Req. Asistencia</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaCategoriasBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div id="tab-tipos" class="tab-content">
            <div class="admin-section">
                <h2>Tipos de Evento</h2>
                <form id="formTipo" class="admin-form">
                    <div class="admin-form-fields">
                        <div><input type="hidden" name="id" id="tipo-id"><label for="tipo-nombre"><span
                                    class="rojo">*</span>Tipo de evento</label><input type="text" name="nombre"
                                id="tipo-nombre" required></div><button type="submit">Guardar Tipo</button>
                    </div>
                </form>
                <div>
                    <h3>Tipos Existentes</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaTiposBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        /**
         * Muestra un mensaje usando la alerta nativa del navegador.
         * El parámetro 'type' se mantiene por compatibilidad, pero no afecta el comportamiento de alert().
         * @param {string} message El texto del mensaje a mostrar.
         * @param {string} type El tipo de mensaje (success, error, info). No afecta la alerta nativa.
         */
        function showToast(message, type = "success") {
            alert(message);
        }

        const botones = document.querySelectorAll(".tab-btn");
        const contenidos = document.querySelectorAll(".tab-content");

        botones.forEach(boton => {
            boton.addEventListener("click", () => {
                botones.forEach(b => b.classList.remove("active"));
                boton.classList.add("active");

                const id = boton.getAttribute("data-tab");
                contenidos.forEach(c => {
                    c.classList.remove("active");
                    if (c.id === id) {
                        c.classList.add("active");
                        // Recarga los datos relevantes al cambiar de pestaña
                        if (id === "tab-categorias" || id === "tab-tipos") {
                            cargarSelects(); // Esto también carga las tablas de categorías y tipos
                        } else if (id === "tab-cursos") {
                            cargarCursos();
                            cargarSelects(); // Para que los selects de curso se llenen
                        }
                    }
                });
            });
        });

        /**
         * Carga la tabla de cursos y llena los selects de tipo y categoría.
         */
        async function cargarCursos() {
            const tabla = document.querySelector("#tablaCursos tbody");
            if (!tabla) return;

            try {
                const res = await fetch("../service/curso.php?action=listar");
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                const cursos = await res.json();
                tabla.innerHTML = cursos.map(c => {
                    const fechaActual = new Date();
                    const fechaInicio = new Date(c.fecha_inicio);
                    const enEjecucion = fechaActual >= fechaInicio;
                    
                    // Determinar clases CSS basadas en estado de cupos y ejecución
                    let estadoClass = '';
                    if (enEjecucion) {
                        estadoClass = 'table-warning';
                    } else if (c.estado_cupos === 'LLENO') {
                        estadoClass = 'table-danger';
                    } else if (c.estado_cupos === 'POCOS_CUPOS') {
                        estadoClass = 'table-warning';
                    }
                    
                    const editButtonDisabled = enEjecucion ? 'disabled' : '';
                    const editButtonTitle = enEjecucion ? 'No se puede editar un curso en ejecución' : 'Editar curso';
                    
                    // Información de cupos con iconos
                    let cuposInfo = `${c.cupos_disponibles}/${c.cupos}`;
                    if (c.estado_cupos === 'LLENO') {
                        cuposInfo += ' <i class="fas fa-exclamation-triangle text-danger" title="Curso lleno"></i>';
                    } else if (c.estado_cupos === 'POCOS_CUPOS') {
                        cuposInfo += ' <i class="fas fa-exclamation-circle text-warning" title="Pocos cupos disponibles"></i>';
                    } else {
                        cuposInfo += ' <i class="fas fa-check-circle text-success" title="Cupos disponibles"></i>';
                    }
                    
                    return `
                    <tr id="curso-row-${c.id}" class="${estadoClass}">
                        <td>${c.id}</td>
                        <td id="curso-nombre-${c.id}">${c.nombre_evento}</td>
                        <td>${c.tipo_nombre}</td>
                        <td>${c.categoria_nombre}</td>
                        <td>${c.ponentes}</td>
                        <td>${c.fecha_inicio}</td>
                        <td>${c.fecha_fin}</td>
                        <td>${c.fecha_inicio_inscripciones}</td>
                        <td>${c.fecha_fin_inscripciones}</td>
                        <td>${c.horas}</td>
                        <td>${cuposInfo}</td>
                        <td>${c.estado}${enEjecucion ? ' (En ejecución)' : ''}</td>
                        <td><img src="${c.ruta_imagen}" alt="imagen" style="width: 50px; height: 50px; object-fit: cover;"></td>
                        <td>
                            <button class="btn btn-sm btn-info edit-curso-inline" data-id="${c.id}" ${editButtonDisabled} title="${editButtonTitle}"><i class="fas fa-edit"></i> Editar</button>
                            <button class="btn btn-sm btn-success save-curso-inline d-none" data-id="${c.id}"><i class="fas fa-save"></i> Guardar</button>
                            <button class="btn btn-sm btn-danger delete-curso" data-id="${c.id}"><i class="fas fa-trash-alt"></i> Eliminar</button>
                        </td>
                    </tr>
                    `;
                }).join("");

                // Añadir event listeners para la edición en línea de cursos
                document.querySelectorAll('.edit-curso-inline').forEach(button => {
                    button.addEventListener('click', (e) => habilitarEdicionCurso(e.currentTarget.dataset.id));
                });
                document.querySelectorAll('.save-curso-inline').forEach(button => {
                    button.addEventListener('click', (e) => guardarEdicionCurso(e.currentTarget.dataset.id));
                });

                // Añadir event listeners para eliminar cursos (este botón sigue igual)
                document.querySelectorAll('.delete-curso').forEach(button => {
                    button.addEventListener('click', (e) => eliminarCurso(e.currentTarget.dataset.id));
                });

            } catch (e) {
                console.error("Error al cargar cursos:", e);
                showToast("Error al cargar cursos. Revisa la consola para más detalles.", "error");
            }
        }

        /**
         * Maneja el envío del formulario de cursos.
         * NOTA: Este formulario ahora se usará principalmente para CREAR cursos,
         * la edición se hace en línea en la tabla.
         */
        document.getElementById("formCurso").addEventListener("submit", async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            // Si el campo oculto 'id' del formulario tiene un valor, significa que estamos editando desde el formulario superior.
            // Si no, es una creación. La edición en línea es independiente de este formulario.
            const action = formData.get("id") ? "editar" : "crear";
            const url = "../service/curso.php?action=" + action;

            try {
                const res = await fetch(url, { method: "POST", body: formData });
                const text = await res.text();

                let result;
                try {
                    result = JSON.parse(text);
                } catch (jsonError) {
                    console.error("Error al parsear JSON:", jsonError);
                    console.error("Respuesta cruda del servidor:", text);
                    showToast("Error inesperado del servidor. Revisa la consola.");
                    return;
                }

                if (result.success) {
                    showToast(result.mensaje || "Curso guardado correctamente.");
                    cargarCursos(); // Recarga la tabla para mostrar los cambios
                    e.target.reset(); // Limpia el formulario
                    document.getElementById("curso-id").value = ''; // Asegura que el ID oculto esté vacío para futuras creaciones
                    // La imagen es requerida para nuevas creaciones, pero al editar, no siempre se sube una nueva.
                    // Si el form es para crear, el input file debería ser requerido. Si es para editar, no.
                    // Esto es algo que tu backend debe manejar, o puedes controlar aquí si el ID está presente.
                    // Por simplicidad, el atributo 'required' se mantiene en el HTML.
                } else {
                    showToast(result.mensaje || "Error al guardar curso.");
                }
            } catch (err) {
                console.error("Error en la petición del curso:", err);
                showToast("Fallo en la conexión al guardar curso.");
            }
        });

        /**
         * Maneja el envío del formulario de categorías (crear).
         * La edición ahora se maneja directamente en la tabla.
         */
        document.getElementById("formCategoria").addEventListener("submit", async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            
            // --- Validación frontend: Al menos un checkbox debe estar marcado ---
            const requiereNota = document.getElementById("cat-requiere-nota").checked;
            const requiereAsistencia = document.getElementById("cat-requiere-asistencia").checked;
            
            if (!requiereNota && !requiereAsistencia) {
                showToast("Debe seleccionar al menos una opción: Requiere calificación o Requiere control de asistencia.");
                return;
            }
            // --- Fin validación frontend ---
            
            const action = formData.get("id") ? "editar" : "crear"; // Aunque 'editar' se manejará diferente
            const url = "../service/categoria_evento.php?action=" + action;

            try {
                const res = await fetch(url, { method: "POST", body: formData });
                const text = await res.text();

                let result;
                try {
                    result = JSON.parse(text);
                } catch (jsonError) {
                    console.error("Error al parsear JSON (Categoría):", jsonError);
                    console.error("Respuesta cruda del servidor (Categoría):", text);
                    showToast("Error inesperado del servidor al guardar categoría.");
                    return;
                }

                if (result.success) {
                    showToast(result.mensaje || "Categoría guardada correctamente.");
                    cargarSelects(); // Recarga la tabla de categorías
                    e.target.reset();
                    document.getElementById("cat-id").value = '';
                } else {
                    showToast(result.mensaje || "Error al guardar categoría.");
                }
            } catch (err) {
                console.error("Error en la petición de categoría:", err);
                showToast("Fallo en la conexión al guardar categoría.");
            }
        });

        /**
         * Maneja el envío del formulario de tipos de evento.
         */
        document.getElementById("formTipo").addEventListener("submit", async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const action = formData.get("id") ? "editar" : "crear";
            const url = "../service/tipo_evento.php?action=" + action;

            try {
                const res = await fetch(url, { method: "POST", body: formData });
                const text = await res.text();

                let result;
                try {
                    result = JSON.parse(text);
                } catch (jsonError) {
                    console.error("Error al parsear JSON (Tipo):", jsonError);
                    console.error("Respuesta cruda del servidor (Tipo):", text);
                    showToast("Error inesperado del servidor al guardar tipo de evento.");
                    return;
                }

                if (result.success) {
                    showToast(result.mensaje || "Tipo de evento guardado correctamente.");
                    cargarSelects();
                    e.target.reset();
                    document.getElementById("tipo-id").value = '';
                } else {
                    showToast(result.mensaje || "Error al guardar tipo de evento.");
                }
            } catch (err) {
                console.error("Error en la petición de tipo:", err);
                showToast("Fallo en la conexión al guardar tipo de evento.");
            }
        });

        /**
         * Carga los selects de tipos de evento y categorías, y sus respectivas tablas.
         */
        async function cargarSelects() {
            try {
                const [tiposRes, categoriasRes] = await Promise.all([
                    fetch("../service/tipo_evento.php?action=listar"),
                    fetch("../service/categoria_evento.php?action=listar")
                ]);

                if (!tiposRes.ok || !categoriasRes.ok) {
                    throw new Error("Una de las respuestas HTTP no fue exitosa.");
                }

                const tipos = await tiposRes.json();
                const categorias = await categoriasRes.json();

                const selTipo = document.getElementById("selectTipo");
                const selCat = document.getElementById("selectCategoria");

                if (selTipo) {
                    selTipo.innerHTML = '<option value="">Seleccionar tipo de evento</option>';
                    tipos.forEach(t => {
                        selTipo.innerHTML += `<option value="${t.id}">${t.nombre}</option>`;
                    });
                }

                if (selCat) {
                    selCat.innerHTML = '<option value="">Seleccionar categoría</option>';
                    categorias.forEach(c => {
                        selCat.innerHTML += `<option value="${c.id}">${c.nombre}</option>`;
                    });
                }

                // Llenar tabla de categorías con edición en línea
                const tablaCat = document.getElementById("tablaCategoriasBody");
                if (tablaCat) {
                    tablaCat.innerHTML = categorias.map(c => `
                    <tr id="categoria-row-${c.id}">
                        <td>${c.id}</td>
                        <td id="categoria-nombre-${c.id}">${c.nombre}</td>
                        <td id="categoria-nota-${c.id}">${c.requiere_nota == 1 ? 'Sí' : 'No'}</td>
                        <td id="categoria-asistencia-${c.id}">${c.requiere_asistencia == 1 ? 'Sí' : 'No'}</td>
                        <td>
                            <button class="btn btn-sm btn-info edit-categoria-inline" data-id="${c.id}" data-nombre="${c.nombre}" data-nota="${c.requiere_nota}" data-asistencia="${c.requiere_asistencia}"><i class="fas fa-edit"></i> Editar</button>
                            <button class="btn btn-sm btn-success save-categoria-inline d-none" data-id="${c.id}"><i class="fas fa-save"></i> Guardar</button>
                        </td>
                    </tr>
                    `).join("");

                    // --- Event Listeners para la edición en línea de Categorías ---
                    document.querySelectorAll('.edit-categoria-inline').forEach(button => {
                        button.addEventListener('click', (e) => habilitarEdicionCategoria(e.currentTarget.dataset.id));
                    });
                    document.querySelectorAll('.save-categoria-inline').forEach(button => {
                        button.addEventListener('click', (e) => guardarEdicionCategoria(e.currentTarget.dataset.id));
                    });
                    // --- Fin Event Listeners Categorías ---
                }

                // Llenar tabla de tipos
                const tablaTipos = document.getElementById("tablaTiposBody");
                if (tablaTipos) {
                    tablaTipos.innerHTML = tipos.map(t => `
                    <tr>
                        <td>${t.id}</td>
                        <td>${t.nombre}</td>
                        <td>
                            <button class="btn btn-sm btn-info edit-tipo" data-id="${t.id}" data-nombre="${t.nombre}"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger delete-tipo" data-id="${t.id}"><i class="fas fa-trash-alt"></i></button>
                        </td>
                    </tr>
                    `).join("");

                    // Añadir event listeners para editar/eliminar tipos
                    document.querySelectorAll('.edit-tipo').forEach(button => {
                        button.addEventListener('click', (e) => editarTipo(e.currentTarget.dataset.id, e.currentTarget.dataset.nombre));
                    });
                    document.querySelectorAll('.delete-tipo').forEach(button => {
                        button.addEventListener('click', (e) => eliminarTipo(e.currentTarget.dataset.id));
                    });
                }

            } catch (e) {
                console.error("Error al cargar selectores y tablas:", e);
                showToast("Error al cargar los datos de categorías y tipos.", "error");
            }
        }

        /**
         * Habilita la edición en línea de un nombre de categoría.
         */
        function habilitarEdicionCategoria(id) {
            const nombreCell = document.getElementById(`categoria-nombre-${id}`);
            const notaCell = document.getElementById(`categoria-nota-${id}`);
            const asistenciaCell = document.getElementById(`categoria-asistencia-${id}`);
            const editButton = document.querySelector(`#categoria-row-${id} .edit-categoria-inline`);
            const saveButton = document.querySelector(`#categoria-row-${id} .save-categoria-inline`);

            if (nombreCell && notaCell && asistenciaCell && editButton && saveButton) {
                const currentName = nombreCell.textContent;
                const currentNota = editButton.dataset.nota == '1';
                const currentAsistencia = editButton.dataset.asistencia == '1';
                
                // Reemplaza el texto con campos de entrada
                nombreCell.innerHTML = `<input type="text" class="form-control" value="${currentName}" id="input-categoria-nombre-${id}">`;
                notaCell.innerHTML = `<input type="checkbox" class="form-check-input" ${currentNota ? 'checked' : ''} id="input-categoria-nota-${id}">`;
                asistenciaCell.innerHTML = `<input type="checkbox" class="form-check-input" ${currentAsistencia ? 'checked' : ''} id="input-categoria-asistencia-${id}">`;

                // Oculta el botón 'Editar' y muestra el botón 'Guardar'
                editButton.classList.add('d-none');
                saveButton.classList.remove('d-none');

                // Pone el foco en el campo de entrada para que el usuario pueda empezar a escribir de inmediato
                document.getElementById(`input-categoria-nombre-${id}`).focus();
            }
        }

        /**
         * Guarda los cambios de una categoría editada en línea.
         */
        async function guardarEdicionCategoria(id) {
            const inputField = document.getElementById(`input-categoria-nombre-${id}`);
            const notaField = document.getElementById(`input-categoria-nota-${id}`);
            const asistenciaField = document.getElementById(`input-categoria-asistencia-${id}`);
            
            const newName = inputField ? inputField.value.trim() : '';

            if (!newName) {
                alert("El nombre de la categoría no puede estar vacío.");
                return;
            }

            // --- Validación: Al menos una opción debe estar marcada ---
            const requiereNota = notaField && notaField.checked;
            const requiereAsistencia = asistenciaField && asistenciaField.checked;
            
            if (!requiereNota && !requiereAsistencia) {
                alert("Debe seleccionar al menos una opción: Requiere calificación o Requiere control de asistencia.");
                return;
            }
            // --- Fin validación ---

            const formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', newName);
            formData.append('requiere_nota', requiereNota ? '1' : '0');
            formData.append('requiere_asistencia', requiereAsistencia ? '1' : '0');

            try {
                const res = await fetch("../service/categoria_evento.php?action=editar", {
                    method: "POST",
                    body: formData
                });
                const text = await res.text();
                let result;
                try {
                    result = JSON.parse(text);
                } catch (jsonError) {
                    console.error("Error al parsear JSON (Guardar Categoría en línea):", jsonError);
                    console.error("Respuesta cruda del servidor (Guardar Categoría en línea):", text);
                    alert("Error inesperado del servidor al guardar categoría. Revisa la consola.");
                    return;
                }

                if (result.success) {
                    alert(result.mensaje || "Categoría actualizada correctamente.");
                    // Recargar toda la tabla para mostrar los cambios y restaurar los botones
                    cargarSelects();
                } else {
                    alert(result.mensaje || "Error al actualizar la categoría.");
                }
            } catch (err) {
                console.error("Error en la petición de guardar categoría en línea:", err);
                alert("Fallo en la conexión al guardar categoría.");
            }
        }

        /**
         * Habilita la edición en línea del nombre de un curso.
         */
        function habilitarEdicionCurso(id) {
            const editButton = document.querySelector(`#curso-row-${id} .edit-curso-inline`);
            
            // Verificar si el botón está deshabilitado (curso en ejecución)
            if (editButton && editButton.disabled) {
                alert("No se puede editar un curso que ya está en ejecución.");
                return;
            }
            
            const nombreCell = document.getElementById(`curso-nombre-${id}`);
            const saveButton = document.querySelector(`#curso-row-${id} .save-curso-inline`);
            const deleteButton = document.querySelector(`#curso-row-${id} .delete-curso`);

            if (nombreCell && editButton && saveButton) {
                const currentName = nombreCell.textContent;
                nombreCell.innerHTML = `<input type="text" class="form-control" value="${currentName}" id="input-curso-nombre-${id}">`;

                editButton.classList.add('d-none');
                saveButton.classList.remove('d-none');
                if (deleteButton) deleteButton.classList.add('d-none');

                document.getElementById(`input-curso-nombre-${id}`).focus();
            }
        }

        /**
         * Guarda los cambios de un curso editado en línea.
         */
        async function guardarEdicionCurso(id) {
            const inputField = document.getElementById(`input-curso-nombre-${id}`);
            const newName = inputField ? inputField.value.trim() : '';

            if (!newName) {
                alert("El nombre del curso no puede estar vacío.");
                return;
            }

            const formData = new FormData();
            formData.append('id', id);
            formData.append('nombre_evento', newName); // Asegúrate que el nombre del campo sea 'nombre_evento' como en tu backend de curso

            try {
                // Aquí, la acción 'editar' para cursos podría requerir más campos en el futuro,
                // pero por ahora solo estamos enviando el ID y el nombre_evento para la edición en línea.
                const res = await fetch("../service/curso.php?action=editar", {
                    method: "POST",
                    body: formData
                });
                const text = await res.text();
                let result;
                try {
                    result = JSON.parse(text);
                } catch (jsonError) {
                    console.error("Error al parsear JSON (Guardar Curso en línea):", jsonError);
                    console.error("Respuesta cruda del servidor (Guardar Curso en línea):", text);
                    alert("Error inesperado del servidor al guardar curso. Revisa la consola.");
                    return;
                }

                if (result.success) {
                    alert(result.mensaje || "Curso actualizado correctamente.");
                    // Recargar toda la tabla para mostrar los cambios y restaurar los botones
                    cargarCursos();
                } else {
                    alert(result.mensaje || "Error al actualizar el curso.");
                }
            } catch (err) {
                console.error("Error en la petición de guardar curso en línea:", err);
                alert("Fallo en la conexión al guardar curso.");
            }
        }


        /**
         * Elimina una categoría.
         * Esta función ya no se llama desde la interfaz si se quitó el botón de eliminar.
         * Se mantiene por si se usa en otro lugar o para referencia.
         */
        async function eliminarCategoria(id) {
            if (!confirm("¿Estás seguro de que quieres eliminar esta categoría?")) {
                return;
            }
            try {
                const res = await fetch(`../service/categoria_evento.php?action=eliminar&id=${id}`, {
                    method: 'GET'
                });
                const text = await res.text();
                let result;
                try {
                    result = JSON.parse(text);
                } catch (jsonError) {
                    console.error("Error al parsear JSON (Eliminar Categoría):", jsonError);
                    console.error("Respuesta cruda del servidor (Eliminar Categoría):", text);
                    showToast("Error inesperado del servidor al eliminar categoría.");
                    return;
                }

                if (result.success) {
                    showToast(result.mensaje || "Categoría eliminada correctamente.");
                    cargarSelects();
                } else {
                    showToast(result.mensaje || "Error al eliminar categoría.");
                }
            } catch (err) {
                console.error("Error al eliminar categoría:", err);
                showToast("Fallo en la conexión al eliminar categoría.");
            }
        }

        /**
         * Edita un tipo de evento (carga al formulario de arriba).
         */
        function editarTipo(id, nombre) {
            document.getElementById("tipo-id").value = id;
            document.getElementById("tipo-nombre").value = nombre;
        }

        /**
         * Elimina un tipo de evento.
         */
        async function eliminarTipo(id) {
            if (!confirm("¿Estás seguro de que quieres eliminar este tipo de evento?")) {
                return;
            }
            try {
                const res = await fetch(`../service/tipo_evento.php?action=eliminar&id=${id}`, {
                    method: 'GET'
                });
                const text = await res.text();
                let result;
                try {
                    result = JSON.parse(text);
                } catch (jsonError) {
                    console.error("Error al parsear JSON (Eliminar Tipo):", jsonError);
                    console.error("Respuesta cruda del servidor (Eliminar Tipo):", text);
                    showToast("Error inesperado del servidor al eliminar tipo de evento.");
                    return;
                }

                if (result.success) {
                    showToast(result.mensaje || "Tipo de evento eliminado correctamente.");
                    cargarSelects();
                } else {
                    showToast(result.mensaje || "Error al eliminar tipo de evento.");
                }
            } catch (err) {
                console.error("Error al eliminar tipo:", err);
                showToast("Fallo en la conexión al eliminar tipo de evento.");
            }
        }

        /**
         * Elimina un curso.
         */
        async function eliminarCurso(id) {
            if (!confirm("¿Estás seguro de que quieres eliminar este curso?")) {
                return;
            }
            try {
                const res = await fetch(`../service/curso.php?action=eliminar&id=${id}`, {
                    method: 'GET'
                });
                const text = await res.text();
                let result;
                try {
                    result = JSON.parse(text);
                } catch (jsonError) {
                    console.error("Error al parsear JSON (Eliminar Curso):", jsonError);
                    console.error("Respuesta cruda del servidor (Eliminar Curso):", text);
                    showToast("Error inesperado del servidor al eliminar curso.");
                    return;
                }

                if (result.success) {
                    showToast(result.mensaje || "Curso eliminado correctamente.");
                    cargarCursos();
                } else {
                    showToast(result.mensaje || "Error al eliminar curso.");
                }
            } catch (err) {
                console.error("Error al eliminar curso:", err);
                showToast("Fallo en la conexión al eliminar curso.");
            }
        }


        // Cargar datos inicialmente al cargar la página
        document.addEventListener("DOMContentLoaded", () => {
            // Carga inicial para la pestaña activa (Categorías)
            cargarSelects();
            // Cargar cursos también si la pestaña de cursos es la activa por defecto
            // Si quieres que 'Cursos' sea la pestaña activa al inicio, cambia 'tab-categorias' a 'tab-cursos' en el HTML.
            // Actualmente, 'tab-categorias' es la activa por defecto.
        });
    </script>
</body>

</html>