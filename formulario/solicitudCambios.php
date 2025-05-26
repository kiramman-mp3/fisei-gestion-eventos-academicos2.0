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

    <!-- ::::::::::::::::::::: HEADER ::::::::::::::::::::: -->
    <header class="top-header">
        <img src="logo_uta.png" alt="Logo UTA">
        <div class="site-name">Universidad<br>Técnica de Ambato</div>
    </header>

    <nav class="nav-bar">
        <a href="#">Inicio</a>
        <a href="#">Cursos</a>
    </nav>

    <!-- ::::::::::::::::::::: CARD PRINCIPAL ::::::::::::::::::::: -->
    <main class="card">
        <h1>Solicitud de cambios:</h1>

        <!-- 🔄 INICIO NUEVO LAYOUT -->
        <div class="main-grid">

            <!-- ===== COL 1: FORMULARIO ===== -->
            <div>

                <!--  Título + Fecha  -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
                    <label>
                        <span class="rojo">Título:</span><br>
                        <input type="text" value="Logo con movimiento">
                    </label>

                    <label>
                        <span class="rojo">Fecha:</span><br>
                        <input type="text" id="fecha" readonly>
                    </label>
                </div>

                <!--  Tipo (radios)  -->
                <fieldset style="margin-top:32px;">
                    <legend><span class="rojo">Tipo:</span></legend>
                    <label><input type="radio" name="tipo" checked> Corrección de Error</label><br>
                    <label><input type="radio" name="tipo"> Interfaz</label><br>
                    <label><input type="radio" name="tipo"> Funcional</label><br>
                    <label><input type="radio" name="tipo"> Otro</label>
                </fieldset>

                <!--  Descripción  -->
                <label style="display:block;margin-top:4px;">
                    <span class="rojo">Descripción:</span><br>
                    <textarea rows="5">He notado que se mueve el logo cuando se le hace click, no me gusta.</textarea>
                </label>

                <!--  Captura  -->
                <label style="display:block;margin-top:30px;">
                    <span class="rojo">Captura de pantalla:</span>
                    <span style="color:var(--gray-600);font-style:italic;">(Opcional)</span>
                    <div class="capture-bar">
                        <span class="file-name">"Captura de pantalla logo #1.jpg"</span>
                        <button type="button" class="btn-capture tomar">Tomar otra</button>
                        <button type="button" class="btn-capture eliminar">Eliminar</button>
                    </div>
                </label>

                <!--  Justificación  -->
                <label style="display:block;margin-top:34px;">
                    <span class="rojo">¿Por qué debería solucionarse este problema?</span><br>
                    <textarea
                        rows="4">Siento que hay personas las cuales son muy sensibles al movimiento y se pueden llegar a marear al momento de hacer click en el botón del logo.</textarea>
                </label>

                <!--  Contexto  -->
                <label style="display:block;margin-top:28px;">
                    <span class="rojo">¿Qué estaba haciendo cuando el problema surgió?</span><br>
                    <textarea
                        rows="4">Estaba matriculándome a un curso y quería volver atrás aplastando en el logo y empezó a girar muy feo.</textarea>
                </label>

                <!--  BOTONES  -->
                <div class="actions">
                    <button type="reset" class="btn cancelar">Cancelar</button>
                    <button type="submit" class="btn enviar">Enviar</button>
                </div>
            </div>

            <!-- ===== COL 2: INFO USUARIO ===== -->
            <fieldset class="user-info">
                <legend>Información del usuario:</legend>
                <input type="text" value="ID de usuario: 1850410612" readonly>
                <input type="text" value="Nombre: Johan Rodríguez" readonly>
                <input type="text" value="Correo: 907johan@gmail.com" readonly>
                <input type="text" value="Rol: Estudiante" readonly>
            </fieldset>

        </div>
        <!-- 🔄 FIN NUEVO LAYOUT -->

    </main>

    <!-- ::::::::::::::::::::: FOOTER ::::::::::::::::::::: -->
    <footer>
        UNIVERSIDAD TÉCNICA DE AMBATO 2025 · Centro de cursos FISEI
    </footer>

    <script>
        // fecha automática
        document.getElementById("fecha").value = new Date().toLocaleDateString("es-EC", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric"
        });
    </script>
</body>

</html>