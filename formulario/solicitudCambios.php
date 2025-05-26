<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <title>Solicitud de Cambios</title>

    <style>
        /* ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       PALETA Y VARIABLES
    :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
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

        /* ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       RESET BÁSICO
    :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
        * {
            box-sizing: border-box;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        html {
            font-size: 15px;
            background: var(--gray-050);
        }

        body {
            margin: 0;
            color: var(--gray-900);
        }

        /* ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       HEADER
    :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
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
            transition: opacity .15s;
        }

        .nav-bar a:hover {
            opacity: .75;
        }

        /* ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       CARD PRINCIPAL
    :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
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

        /* ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       NUEVO LAYOUT
       ┌───────────────┬────────────┐
       │ COL 1 (form)  │  COL 2     │
       └───────────────┴────────────┘
    :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 310px;
            gap: 36px;
        }

        /* ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       INPUTS & TEXTAREAS
    :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            border: 1px solid var(--gray-200);
            border-radius: 14px;
            padding: 8px 14px;
            font-size: 1rem;
            transition: border 0.15s;
        }

        input:focus,
        textarea:focus {
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

        /* ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       FIELDSETS
    :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
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

        /* :::::  “Tipo” – radios con color  */
        input[type="radio"] {
            accent-color: var(--maroon);
        }

        /* ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       INFO USUARIO
    :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
        .user-info {
            align-self: flex-start;
            height: auto;
        }

        .user-info input {
            margin-top: 10px;
            background: var(--gray-100);
        }

        /* ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       CAPTURA
    :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
        .capture-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid var(--gray-200);
            border-radius: 9999px;
            padding: 6px 14px;
            font-size: .96rem;
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
            font-size: .9rem;
            color: #fff;
            cursor: pointer;
            transition: background .15s;
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

        /* ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       BOTONES FINALES
    :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
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
            transition: background .18s, transform .1s;
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

        /* ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       FOOTER
    :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
        footer {
            background: var(--maroon);
            color: #fff;
            font-weight: 600;
            text-align: center;
            padding: 14px 8px;
        }

        /* ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
       RESPONSIVE
    :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
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
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Solicitud de Cambios</title>
        <style>
            /* ======== RESET & BASE ======== */
            * {
                box-sizing: border-box;
                font-family: Arial, sans-serif;
                font-size: 14px;
            }

            html,
            body {
                margin: 0;
                background: #f8f8f8;
            }

            :root {
                --rojo: #7c0a0a;
                --gray-600: #6b7280;
                --card-bg: #ffffff;
            }

            /* ======== LAYOUT ======== */
            .top-header {
                background: var(--rojo);
                color: #fff;
                padding: 12px 20px;
                font-weight: bold;
                line-height: 1.1;
            }

            .site-name {
                font-size: 20px;
            }

            .card {
                background: var(--card-bg);
                max-width: 900px;
                margin: 32px auto;
                padding: 32px 40px;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            }

            .card h1 {
                margin-top: 0;
                color: var(--rojo);
                font-size: 24px;
            }

            .main-grid {
                display: grid;
                grid-template-columns: 1fr 280px;
                gap: 40px;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .main-grid {
                    grid-template-columns: 1fr;
                }
            }

            input[type="text"],
            input[type="email"],
            textarea {
                width: 100%;
                padding: 10px 12px;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                resize: vertical;
            }

            input[readonly] {
                background: #f1f5f9;
                cursor: default;
            }

            textarea {
                min-height: 90px;
            }

            fieldset {
                border: 1px solid #d1d5db;
                border-radius: 8px;
                padding: 14px 18px;
            }

            legend {
                padding: 0 6px;
                color: var(--rojo);
                font-weight: bold;
            }

            .rojo {
                color: var(--rojo);
                font-weight: bold;
            }

            .capture-bar {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-top: 6px;
                padding: 8px 12px;
                background: #f1f5f9;
                border: 1px solid #d1d5db;
                border-radius: 8px;
            }

            .file-name {
                flex: 1 1 auto;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .btn-capture,
            .btn {
                cursor: pointer;
                border: none;
                padding: 8px 14px;
                border-radius: 6px;
                transition: 0.2s ease all;
            }

            .btn-capture {
                background: #e5e7eb;
            }

            .btn-capture:hover {
                background: #d1d5db;
            }

            .btn {
                background: var(--rojo);
                color: #fff;
                font-weight: bold;
            }

            .btn:hover {
                opacity: 0.9;
            }

            .btn.cancelar {
                background: #6b7280;
            }

            .actions {
                display: flex;
                gap: 12px;
                margin-top: 28px;
            }

            /* ======== USER INFO ======== */
            .user-info {
                display: flex;
                flex-direction: column;
                gap: 14px;
                border-color: #d1d5db;
                border-radius: 12px;
            }

            .guardar-user,
            .mini-btn {
                align-self: flex-start;
                font-size: 12px;
                padding: 6px 10px;
                background: #0ea5e9;
                color: #fff;
                border: none;
                border-radius: 6px;
            }

            .mini-btn {
                background: #ef4444;
            }
        </style>
    </head>

    <body>
        <!-- ::::::::::::::::::::: HEADER ::::::::::::::::::::: -->
        <header class="top-header">
            <div class="site-name">Universidad<br />Técnica de Ambato</div>
        </header>

        <!-- ::::::::::::::::::::: CARD PRINCIPAL ::::::::::::::::::::: -->
        <main class="card">
            <h1>Solicitud de cambios:</h1>

            <form id="changeForm" class="main-grid" autocomplete="off">
                <!-- ===== COL 1: FORMULARIO ===== -->
                <div>
                    <!--  Título + Fecha  -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                        <label>
                            <span class="rojo">Título:</span><br />
                            <input type="text" id="titulo" name="titulo" placeholder="Título del cambio" />
                        </label>

                        <label>
                            <span class="rojo">Fecha:</span><br />
                            <input type="text" id="fecha" name="fecha" readonly />
                        </label>
                    </div>

                    <!--  Tipo (radios)  -->
                    <fieldset style="margin-top: 32px;">
                        <legend><span class="rojo">Tipo:</span></legend>
                        <label><input type="radio" name="tipo" value="error" /> Corrección de Error</label><br />
                        <label><input type="radio" name="tipo" value="interfaz" /> Interfaz</label><br />
                        <label><input type="radio" name="tipo" value="funcional" /> Funcional</label><br />
                        <label><input type="radio" name="tipo" value="otro" /> Otro</label>
                    </fieldset>

                    <!--  Descripción  -->
                    <label style="display: block; margin-top: 4px;">
                        <span class="rojo">Descripción:</span><br />
                        <textarea id="descripcion" name="descripcion" rows="5"
                            placeholder="Describe el problema..."></textarea>
                    </label>

                    <!--  Captura  -->
                    <label style="display: block; margin-top: 30px;">
                        <span class="rojo">Captura de pantalla:</span>
                        <span style="color: var(--gray-600); font-style: italic;">(Opcional)</span>
                        <div class="capture-bar" id="captureBar">
                            <span class="file-name" id="fileName">Ningún archivo seleccionado</span>
                            <button type="button" class="btn-capture tomar" id="takeShotBtn">Tomar</button>
                            <button type="button" class="btn-capture eliminar" id="deleteShotBtn"
                                style="display: none;">Eliminar</button>
                        </div>
                    </label>

                    <!--  Justificación  -->
                    <label style="display: block; margin-top: 34px;">
                        <span class="rojo">¿Por qué debería solucionarse este problema?</span><br />
                        <textarea id="justificacion" name="justificacion" rows="4"
                            placeholder="Explica la importancia..."></textarea>
                    </label>

                    <!--  Contexto  -->
                    <label style="display: block; margin-top: 28px;">
                        <span class="rojo">¿Qué estaba haciendo cuando el problema surgió?</span><br />
                        <textarea id="contexto" name="contexto" rows="4"
                            placeholder="Describe el contexto..."></textarea>
                    </label>

                    <!--  BOTONES  -->
                    <div class="actions">
                        <button type="reset" class="btn cancelar">Cancelar</button>
                        <button type="submit" class="btn enviar">Enviar</button>
                    </div>
                </div>

                <!-- ===== COL 2: INFO USUARIO ===== -->
                <fieldset class="user-info" id="userFieldset">
                    <legend>Información del usuario:</legend>
                    <input id="uid" name="uid" type="text" placeholder="ID de usuario" />
                    <input id="uname" name="uname" type="text" placeholder="Nombre completo" />
                    <input id="uemail" name="uemail" type="email" placeholder="Correo" />
                    <input id="urol" name="urol" type="text" placeholder="Rol" />
                    <!--  Botón mostrará/ocultará según exista o no sesión  -->
                    <button type="button" class="guardar-user" id="saveUserBtn" style="display: none;">Guardar mis
                        datos</button>
                    <button type="button" class="mini-btn" id="changeUserBtn" style="display: none;">Cambiar
                        usuario</button>
                </fieldset>
            </form>
        </main>

        <!-- ::::::::::::::::::::: JS ::::::::::::::::::::: -->
        <script>
            // Set today date on load
            document.addEventListener("DOMContentLoaded", () => {
                const fechaInput = document.getElementById("fecha");
                const today = new Date();
                fechaInput.value = today.toLocaleDateString("es-EC");

                // ======== USER SESSION HANDLING ========
                const userFieldset = document.getElementById("userFieldset");
                const saveBtn = document.getElementById("saveUserBtn");
                const changeBtn = document.getElementById("changeUserBtn");

                // Try to read user session from localStorage
                const userSession = JSON.parse(localStorage.getItem("userInfo"));

                if (userSession) {
                    // Populate & lock fields
                    ["uid", "uname", "uemail", "urol"].forEach((id) => {
                        const el = document.getElementById(id);
                        el.value = userSession[id] || "";
                        el.readOnly = true;
                    });
                    saveBtn.style.display = "none";
                    changeBtn.style.display = "inline-block";
                } else {
                    saveBtn.style.display = "inline-block";
                    changeBtn.style.display = "none";
                }

                // Save user data
                saveBtn.addEventListener("click", () => {
                    const data = {
                        uid: document.getElementById("uid").value.trim(),
                        uname: document.getElementById("uname").value.trim(),
                        uemail: document.getElementById("uemail").value.trim(),
                        urol: document.getElementById("urol").value.trim(),
                    };
                    if (!data.uid || !data.uname || !data.uemail) {
                        alert("Por favor complete todos los datos de usuario antes de guardar.");
                        return;
                    }
                    localStorage.setItem("userInfo", JSON.stringify(data));
                    location.reload();
                });

                // Change user (clear session)
                changeBtn.addEventListener("click", () => {
                    localStorage.removeItem("userInfo");
                    location.reload();
                });

                // ======== CAPTURE BUTTONS (dummy implementation) ========
                const takeShotBtn = document.getElementById("takeShotBtn");
                const deleteShotBtn = document.getElementById("deleteShotBtn");
                const fileNameSpan = document.getElementById("fileName");

                takeShotBtn.addEventListener("click", () => {
                    // Aquí integraría lógica real de captura 📸
                    fileNameSpan.textContent = "captura.png";
                    deleteShotBtn.style.display = "inline-block";
                });

                deleteShotBtn.addEventListener("click", () => {
                    fileNameSpan.textContent = "Ningún archivo seleccionado";
                    deleteShotBtn.style.display = "none";
                });
            });
        </script>

    </body>

    </html>