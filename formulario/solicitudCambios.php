<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Solicitud de Cambios</title>

    <!-- =================  ESTILOS PRINCIPALES  ================= -->
    <style>
        /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       PALETA Y VARIABLES
    ::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
        :root {
            --maroon: #7c0a0a;
            --maroon-dark: #5b0101;
            --gray-050: #f8f8f8;
            --gray-100: #eeeeee;
            --gray-200: #d8d8d8;
            --gray-600: #6b7280;
            --gray-900: #1f2937;
            --btn-gray: #9ca3af;
            --btn-gray-hover: #6b7280;
        }

        /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       RESET BÁSICO
    ::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
        * {
            box-sizing: border-box;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 18px;
        }

        html {
            font-size: 15px;
            background: var(--gray-050);
        }

        body {
            margin: 0;
            color: var(--gray-900);
        }

        /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       HEADER & NAV
    ::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
        .top-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 18px;
            background: #fff;
            border-bottom: 2px solid var(--gray-200);
        }

        .top-header img {
            height: 46px;
        }

        .site-name {
            font-weight: 700;
            line-height: 1.15;
        }

        .nav-bar {
            background: var(--maroon);
            color: #fff;
            padding: 10px 18px;
            display: flex;
            gap: 22px;
        }

        .nav-bar a {
            color: inherit;
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.15s;
        }

        .nav-bar a:hover {
            opacity: 0.75;
        }

        /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       CARD PRINCIPAL
    ::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
        .card {
            max-width: 1000px;
            margin: 30px auto 44px;
            background: #fff;
            border-radius: 18px;
            padding: 36px 40px 44px;
            box-shadow: 0 4px 20px rgb(0 0 0 / 6%);
        }

        h1 {
            margin: 0 0 32px;
            font-size: 2.1rem;
            color: var(--maroon);
        }

        /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       NUEVO LAYOUT  (form   |   usuario)
    ::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 310px;
            gap: 36px;
        }

        /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       INPUTS, SELECTS & TEXTAREAS
    ::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
        input[type="text"],
        input[type="email"],
        textarea,
        select {
            width: 100%;
            border: 1px solid var(--gray-200);
            border-radius: 14px;
            padding: 8px 14px;
            font-size: 1rem;
            transition: border 0.15s;
            background: #fff;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: var(--maroon);
        }

        textarea {
            resize: vertical;
        }

        label span.rojo {
            color: var(--maroon);
            font-weight: 600;
        }

        /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       FIELDSETS & RADIO
    ::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
        fieldset {
            border: 1px solid var(--gray-200);
            border-radius: 16px;
            padding: 14px 18px 20px;
            margin-bottom: 28px;
        }

        legend {
            font-weight: 600;
            padding: 0 6px;
        }

        input[type="radio"] {
            accent-color: var(--maroon);
        }

        /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       INFO USUARIO
    ::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
        .user-info {
            align-self: flex-start;
        }

        .user-info input {
            margin-top: 10px;
            background: var(--gray-100);
        }

        /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       CAPTURA
    ::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
        .capture-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid var(--gray-200);
            border-radius: 9999px;
            padding: 6px 14px;
            font-size: 0.96rem;
            overflow: hidden;
            margin-top: 6px;
        }

        .file-name {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .btn-capture {
            border: none;
            border-radius: 9999px;
            padding: 4px 18px;
            font-size: 0.9rem;
            color: #fff;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-capture.tomar {
            background: var(--btn-gray);
        }

        .btn-capture.tomar:hover {
            background: var(--btn-gray-hover);
        }

        .btn-capture.eliminar {
            background: var(--maroon);
        }

        .btn-capture.eliminar:hover {
            background: var(--maroon-dark);
        }

        /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       BOTONES FINALES
    ::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 26px;
            margin-top: 38px;
        }

        .btn {
            border: none;
            border-radius: 9999px;
            font-size: 1.1rem;
            font-weight: 700;
            padding: 10px 46px;
            cursor: pointer;
            transition: background 0.18s, transform 0.1s;
        }

        .btn.cancelar {
            background: var(--btn-gray);
            color: #fff;
        }

        .btn.cancelar:hover {
            background: var(--btn-gray-hover);
        }

        .btn.enviar {
            background: var(--maroon);
            color: #fff;
        }

        .btn.enviar:hover {
            background: var(--maroon-dark);
        }

        .btn:active {
            transform: translateY(1px);
        }

        /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       FOOTER
    ::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
        footer {
            background: var(--maroon);
            color: #fff;
            font-weight: 600;
            text-align: center;
            padding: 14px 8px;
        }

        /* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       RESPONSIVE
    ::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
        @media (max-width: 860px) {
            .main-grid {
                grid-template-columns: 1fr;
            }

            .actions {
                flex-direction: column;
                align-items: stretch;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <header class="top-header">
        <div class="site-name">Universidad<br>Técnica de Ambato</div>
    </header>

    <main class="card">
        <h1>Solicitud de cambios:</h1>

        <form id="solicitudForm" novalidate>
            <div class="main-grid">

                <div>
                    <!-- Título + Fecha -->
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

                    <!-- Tipo -->
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

                    <!-- Descripción -->
                    <label style="display:block;margin-top:24px;">
                        <span class="rojo">Descripción:</span><br />
                        <textarea placeholder="Describe el problema..." id="descripcion" name="descripcion" rows="5" required></textarea>
                    </label>

                    <!-- Captura -->
                    <label style="display:block;margin-top:30px;">
                        <span class="rojo">Captura de pantalla:</span>
                        <span style="color:var(--gray-600);font-style:italic;">(Opcional)</span>
                        <!-- input file oculto -->
                        <input type="file" id="captureInput" accept="image/*" style="display:none;" />
                        <!-- barra cuando hay archivo -->
                        <div class="capture-bar" id="captureBar" style="display:none;">
                            <span class="file-name" id="fileName"></span>
                            <button type="button" class="btn-capture tomar" id="takeAnotherBtn">Tomar otra</button>
                            <button type="button" class="btn-capture eliminar" id="deleteCaptureBtn">Eliminar</button>
                        </div>

                        <!-- botón inicial -->
                        <button type="button" class="btn-capture tomar" id="addCaptureBtn">Subir imagen</button>
                    </label>

                    <!-- Justificación -->
                    <label style="display:block;margin-top:34px;">
                        <span class="rojo">¿Por qué debería solucionarse este problema?</span><br />
                        <textarea placeholder="Explica la importancia..." id="justificacion" name="justificacion" rows="4"></textarea>
                    </label>

                    <!-- Contexto -->
                    <label style="display:block;margin-top:28px;">
                        <span class="rojo">¿Qué estaba haciendo cuando el problema surgió?</span><br />
                        <textarea placeholder="Describe el contexto..." id="contexto" name="contexto" rows="4"></textarea>
                    </label>

                    <!-- BOTONES -->
                    <div class="actions">
                        <button type="reset" class="btn cancelar">Cancelar</button>
                        <button type="submit" class="btn enviar">Enviar</button>
                    </div>
                </div>

                <!-- ========== COL 2 : INFO USUARIO ========== -->
                <fieldset class="user-info" id="userFieldset">
                    <legend>Información del usuario:</legend>

                    <input id="uid" name="uid" type="text" placeholder="ID de usuario" required />
                    <input id="uname" name="uname" type="text" placeholder="Nombre completo" required />
                    <input id="uemail" name="uemail" type="email" placeholder="Correo" required />
                    <input id="urol" name="urol" type="text" placeholder="Rol" required />

                    <button type="button" class="guardar-user" id="saveUserBtn"
                        style="display:none;margin-top:12px;">Guardar mis datos</button>
                    <button type="button" class="mini-btn" id="changeUserBtn"
                        style="display:none;margin-top:12px;">Cambiar usuario</button>
                </fieldset>

            </div><!-- /.main-grid -->
        </form>
    </main>

    <footer>© 2025 Universidad Técnica de Ambato</footer>

    <!-- =================  SCRIPTS  ================= -->
    <script>
        // :::::::::::::::::::::::::::::::::::::::::::::::
        //  FECHA (solo lectura)
        // :::::::::::::::::::::::::::::::::::::::::::::::
        const fechaEl = document.getElementById("fecha");
        fechaEl.value = new Date().toLocaleDateString("es-EC", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
        });

        // :::::::::::::::::::::::::::::::::::::::::::::::
        //  GESTIÓN DE USUARIO con localStorage
        // :::::::::::::::::::::::::::::::::::::::::::::::
        const userFieldset = document.getElementById("userFieldset");
        const saveBtn = document.getElementById("saveUserBtn");
        const changeBtn = document.getElementById("changeUserBtn");

        const uid = document.getElementById("uid");
        const uname = document.getElementById("uname");
        const uemail = document.getElementById("uemail");
        const urol = document.getElementById("urol");

        function loadUser() {
            const stored = JSON.parse(localStorage.getItem("userInfo") || "null");

            if (stored) {
                uid.value = stored.uid;
                uname.value = stored.uname;
                uemail.value = stored.uemail;
                urol.value = stored.urol;

                [...userFieldset.querySelectorAll("input")].forEach((i) => (i.readOnly = true));
                saveBtn.style.display = "none";
                changeBtn.style.display = "inline-block";
            } else {
                [...userFieldset.querySelectorAll("input")].forEach((i) => (i.readOnly = false));
                saveBtn.style.display = "inline-block";
                changeBtn.style.display = "none";
            }
        }

        saveBtn.addEventListener("click", () => {
            const info = {
                uid: uid.value.trim(),
                uname: uname.value.trim(),
                uemail: uemail.value.trim(),
                urol: urol.value.trim(),
            };

            if (Object.values(info).some((v) => !v)) {
                alert("Por favor, completa todos los datos de usuario.");
                return;
            }

            localStorage.setItem("userInfo", JSON.stringify(info));
            loadUser();
        });

        changeBtn.addEventListener("click", () => {
            localStorage.removeItem("userInfo");
            loadUser();
        });

        loadUser();

        // :::::::::::::::::::::::::::::::::::::::::::::::
        //  UPLOAD CAPTURA
        // :::::::::::::::::::::::::::::::::::::::::::::::
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

        // :::::::::::::::::::::::::::::::::::::::::::::::
        //  ENVÍO DEL FORMULARIO (solo demo)
        // :::::::::::::::::::::::::::::::::::::::::::::::
        document.getElementById("solicitudForm").addEventListener("submit", (e) => {
            e.preventDefault();
            alert("✔️ Solicitud enviada (demo)\nAquí iría tu lógica de envío al servidor.");
            e.target.reset();
            captureBar.style.display = "none";
            addCaptureBtn.style.display = "inline-block";
            loadUser(); // Mantén datos de user
        });
    </script>
</body>

</html>