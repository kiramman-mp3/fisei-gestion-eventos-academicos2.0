<?php
session_start();
// if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
//     header('Location: ../login.php');
//     exit();
// }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Evento - FISEI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<header class="top-header">
    <img src="../resource/logo-uta.png" alt="Logo UTA">
    <div class="site-name">Eventos Académicos FISEI</div>
</header>

<main class="card-custom">
    <h1 class="text-center">Crear Curso/Evento</h1>

    <form method="POST" action="procesarEvento.php" id="crearEventoForm">
        <div class="mb-3">
            <label for="nombre">Nombre del evento <span class="rojo">*</span></label>
            <input type="text" id="nombre" name="nombre" required>
        </div>

        <div class="mb-3">
            <label for="tipo">Tipo de evento</label>
            <select id="tipo" name="tipo" required>
                <option value="curso">Curso</option>
                <option value="evento">Evento</option>
            </select>
        </div>

        <!-- Fechas para curso -->
        <div id="cursoFechas">
            <div class="mb-3">
                <label>Fecha de inicio <span class="rojo">*</span></label>
                <div class="d-flex gap-2">
                    <select name="inicio_dia" id="inicio_dia" class="form-select rounded-pill"></select>
                    <select name="inicio_mes" id="inicio_mes" class="form-select rounded-pill"></select>
                    <select name="inicio_anio" id="inicio_anio" class="form-select rounded-pill"></select>
                </div>
            </div>

            <div class="mb-3">
                <label>Fecha de fin <span class="rojo">*</span></label>
                <div class="d-flex gap-2">
                    <select name="fin_dia" id="fin_dia" class="form-select rounded-pill"></select>
                    <select name="fin_mes" id="fin_mes" class="form-select rounded-pill"></select>
                    <select name="fin_anio" id="fin_anio" class="form-select rounded-pill"></select>
                </div>
            </div>
        </div>

        <!-- Fecha y hora para evento único -->
        <div id="eventoFecha" style="display: none;">
            <div class="mb-3">
                <label>Fecha del evento <span class="rojo">*</span></label>
                <div class="d-flex gap-2">
                    <select name="evento_dia" id="evento_dia" class="form-select rounded-pill"></select>
                    <select name="evento_mes" id="evento_mes" class="form-select rounded-pill"></select>
                    <select name="evento_anio" id="evento_anio" class="form-select rounded-pill"></select>
                </div>
            </div>

            <div class="mb-3">
                <label for="hora">Hora del evento <span class="rojo">*</span></label>
                <input type="time" id="hora" name="hora" class="form-control rounded-pill">
            </div>
        </div>

        <div class="mb-3">
            <label for="cupos">Número de cupos <span class="rojo">*</span></label>
            <input type="number" id="cupos" name="cupos" required min="1">
        </div>

        <div class="mb-3">
            <label for="requisitos_texto">Requisitos generales</label>
            <textarea id="requisitos_texto" name="requisitos_texto" rows="4" placeholder="Ejemplo: ser estudiante activo, presentar cédula..."></textarea>
        </div>

        <div class="mb-4">
            <label for="pago">Tipo de pago</label>
            <select id="pago" name="pago">
                <option value="gratis">Gratis</option>
                <option value="pagado">Pagado</option>
            </select>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-primary">Crear Evento</button>
            <a href="panel_admin.php" class="btn cancelar">Cancelar</a>
        </div>
    </form>
</main>

<footer class="footer-expandido">
    <div class="footer-container">
        <div class="footer-section">
            <h5>Sobre el sistema</h5>
            <ul>
                <li><a href="#"><i class="fa-solid fa-circle-question"></i> ¿Qué es Eventos FISEI?</a></li>
                <li><a href="#"><i class="fa-solid fa-book"></i> Manual de usuario</a></li>
                <li><a href="#"><i class="fa-solid fa-code-branch"></i> Versiones</a></li>
                <li><a href="#"><i class="fa-solid fa-user-group"></i> Créditos</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h5>Soporte</h5>
            <ul>
                <li><a href="#"><i class="fa-solid fa-circle-info"></i> Preguntas frecuentes</a></li>
                <li><a href="../formulario/solicitud_cambios.php"><i class="fa-solid fa-bug"></i> Reportar un error</a></li>
                <li><a href="#"><i class="fa-solid fa-headset"></i> Solicitar ayuda</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h5>Legal</h5>
            <ul>
                <li><a href="#"><i class="fa-solid fa-file-contract"></i> Términos de uso</a></li>
                <li><a href="#"><i class="fa-solid fa-user-shield"></i> Política de privacidad</a></li>
                <li><a href="#"><i class="fa-solid fa-scroll"></i> Licencia</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h5>FISEI - UTA</h5>
            <p>Facultad de Ingeniería en Sistemas,<br> Electrónica e Industrial</p>
            <div class="footer-social">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        © <?= date('Y') ?> FISEI - Universidad Técnica de Ambato. Todos los derechos reservados.
    </div>
</footer>

<script>
    const dias = [...Array(31).keys()].map(d => d + 1);
    const meses = [
        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    ];
    const anios = Array.from({ length: 10 }, (_, i) => 2025 + i);

    function llenarSelect(id, datos) {
        const select = document.getElementById(id);
        datos.forEach(valor => {
            const opt = document.createElement("option");
            opt.value = valor;
            opt.text = valor;
            select.appendChild(opt);
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        const tipo = document.getElementById("tipo");
        const cursoFechas = document.getElementById("cursoFechas");
        const eventoFecha = document.getElementById("eventoFecha");

        const camposCurso = ["nombre", "cupos", "inicio_dia", "inicio_mes", "inicio_anio", "fin_dia", "fin_mes", "fin_anio"];
        const camposEvento = ["nombre", "cupos", "evento_dia", "evento_mes", "evento_anio", "hora"];

        ["inicio_dia", "inicio_mes", "inicio_anio", "fin_dia", "fin_mes", "fin_anio", "evento_dia", "evento_mes", "evento_anio"].forEach(id => {
            if (id.includes("dia")) llenarSelect(id, dias);
            else if (id.includes("mes")) llenarSelect(id, meses);
            else llenarSelect(id, anios);
        });

        tipo.addEventListener("change", () => {
            if (tipo.value === "curso") {
                cursoFechas.style.display = "block";
                eventoFecha.style.display = "none";
            } else {
                cursoFechas.style.display = "none";
                eventoFecha.style.display = "block";
            }
        });

        document.getElementById("crearEventoForm").addEventListener("submit", function (e) {
            const tipoActual = tipo.value;
            const campos = tipoActual === "curso" ? camposCurso : camposEvento;
            let formIsValid = true;

            campos.forEach((campoId) => {
                const campo = document.getElementById(campoId);
                if (!campo || campo.value.trim() === "") {
                    campo.style.borderColor = "red";
                    formIsValid = false;
                } else {
                    campo.style.borderColor = "";
                }
            });

            if (!formIsValid) {
                alert("Por favor, completa todos los campos obligatorios.");
                e.preventDefault();
            }
        });

        [...camposCurso, ...camposEvento].forEach(id => {
            const campo = document.getElementById(id);
            if (campo) {
                campo.addEventListener("input", () => {
                    if (campo.value.trim() !== "") {
                        campo.style.borderColor = "";
                    }
                });
            }
        });
    });
</script>

</body>
</html>
