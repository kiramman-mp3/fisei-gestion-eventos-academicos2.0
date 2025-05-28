<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Solicitud de Cambios</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        main {
            padding-bottom: 800px !important;
        }
    </style>
</head>

<body>
    <header class="top-header">
        <div class="site-name">Universidad<br>Técnica de Ambato</div>
    </header>

    <main class="card">
        <h1>Solicitud de cambios:</h1>

        <form id="solicitudForm" action="guardar_solicitud.php" method="POST" enctype="multipart/form-data" novalidate>
            <div class="main-grid">
                <div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
                        <label>
                            <span class="rojo">Título:</span><br />
                            <input type="text" id="titulo" name="titulo" required />
                        </label>

                        <label>
                            <span class="rojo">Fecha:</span><br />
                            <input type="text" id="fecha" name="fecha" readonly />
                        </label>
                    </div>

                    <label style="display:block;margin-top:28px;">
                        <span class="rojo">Tipo:</span><br />
                        <select id="tipo" name="tipo" required>
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="Corrección de Error">Corrección de Error</option>
                            <option value="Interfaz">Interfaz</option>
                            <option value="Funcional">Funcional</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </label>

                    <label style="display:block;margin-top:24px;">
                        <span class="rojo">Descripción:</span><br />
                        <textarea id="descripcion" name="descripcion" rows="5" required
                            placeholder="Describe el problema..."></textarea>
                    </label>

                    <label style="display:block;margin-top:30px;">
                        <span class="rojo">Captura de pantalla:</span>
                        <span style="color:var(--gray-600);font-style:italic;">(Opcional)</span>

                        <!-- Campo oculto real -->
                        <input type="file" id="captureInput" name="captura" accept="image/*" style="display:none;" />

                        <!-- Barra que aparece cuando hay archivo cargado -->
                        <div class="capture-bar" id="captureBar" style="display:none;">
                            <span class="file-name" id="fileName"></span>
                            <button type="button" class="btn-capture tomar" id="takeAnotherBtn">Tomar otra</button>
                            <button type="button" class="btn-capture eliminar" id="deleteCaptureBtn">Eliminar</button>
                        </div>

                        <!-- Botón inicial para subir imagen -->
                        <button type="button" class="btn-capture tomar" id="addCaptureBtn">Subir imagen</button>
                    </label>


                    <label style="display:block;margin-top:34px;">
                        <span class="rojo">¿Por qué debería solucionarse este problema?</span><br />
                        <textarea id="justificacion" name="justificacion" rows="4"
                            placeholder="Explica la importancia..."></textarea>
                    </label>

                    <label style="display:block;margin-top:28px;">
                        <span class="rojo">¿Qué estaba haciendo cuando el problema surgió?</span><br />
                        <textarea id="contexto" name="contexto" rows="4"
                            placeholder="Describe el contexto..."></textarea>
                    </label>

                    <div class="actions">
                        <button type="reset" class="btn cancelar">Cancelar</button>
                        <button type="submit" class="btn enviar">Enviar</button>
                    </div>
                </div>

                <fieldset class="user-info" id="userFieldset">
                    <legend>Información del usuario:</legend>
                    <input id="uid" name="uid" type="text" placeholder="ID de usuario" required />
                    <input id="uname" name="uname" type="text" placeholder="Nombre completo" required />
                    <input id="uemail" name="uemail" type="email" placeholder="Correo" required />
                    <input id="urol" name="urol" type="text" placeholder="Rol" required />
                </fieldset>
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
                    <li><a href="../informativo/nosotros.php"><i class="fa-solid fa-user-group"></i> Créditos</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h5>Soporte</h5>
                <ul>
                    <li><a href="#"><i class="fa-solid fa-circle-info"></i> Preguntas frecuentes</a></li>
                    <li><a href="#"><i class="fa-solid fa-bug"></i> Reportar un error</a></li>
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
        // Mostrar fecha actual en campo readonly
        document.getElementById("fecha").value = new Date().toLocaleDateString("es-EC", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
        });

        // Lógica de subida de imagen personalizada
        const captureInput = document.getElementById("captureInput");
        const captureBar = document.getElementById("captureBar");
        const fileNameSpan = document.getElementById("fileName");
        const addCaptureBtn = document.getElementById("addCaptureBtn");
        const takeAnotherBtn = document.getElementById("takeAnotherBtn");
        const deleteCaptureBtn = document.getElementById("deleteCaptureBtn");

        addCaptureBtn.addEventListener("click", () => captureInput.click());
        takeAnotherBtn.addEventListener("click", () => captureInput.click());

        deleteCaptureBtn.addEventListener("click", () => {
            captureInput.value = "";
            captureBar.style.display = "none";
            addCaptureBtn.style.display = "inline-block";
        });

        captureInput.addEventListener("change", () => {
            if (captureInput.files && captureInput.files[0]) {
                fileNameSpan.textContent = captureInput.files[0].name;
                captureBar.style.display = "flex";
                addCaptureBtn.style.display = "none";
            }
        });

        document.getElementById("solicitudForm").addEventListener("submit", function (e) {
            const camposObligatorios = [
                "titulo",
                "fecha",
                "tipo",
                "descripcion",
                "justificacion",
                "contexto",
                "uid",
                "uname",
                "uemail",
                "urol",
            ];

            let formIsValid = true;

            camposObligatorios.forEach((campoId) => {
                const campo = document.getElementById(campoId);
                if (!campo || campo.value.trim() === "") {
                    campo.style.borderColor = "red";
                    formIsValid = false;
                } else {
                    campo.style.borderColor = ""; // limpiar si ya estaba rojo
                }
            });

            if (!formIsValid) {
                alert("Por favor, completa todos los campos obligatorios.");
                e.preventDefault(); // bloquear envío
            }
        });

        camposObligatorios.forEach((id) => {
            const campo = document.getElementById(id);
            campo.addEventListener("input", () => {
                if (campo.value.trim() !== "") {
                    campo.style.borderColor = "";
                }
            });
        });
    </script>

</body>