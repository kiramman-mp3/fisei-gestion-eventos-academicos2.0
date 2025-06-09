<?php
require_once '../session.php';
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

require_once '../session.php';
$uid = getUserId() ?? '';
$uname = getUserName() . ' ' . getUserLastname() ?? '';
$uemail = getUserEmail() ?? '';
$urol = getUserRole() ?? '';

$sesion_activa = !empty($uid) && !empty($uname) && !empty($uemail) && !empty($urol);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Solicitud de Cambios</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .alert-success {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #d4edda;
            color: #155724;
            padding: 12px 16px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            z-index: 9999;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success .close-btn {
            background: none;
            border: none;
            color: #155724;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }
    </style>
</head>

<body>
<header class="top-header d-flex justify-content-between align-items-center px-4 py-2 shadow-sm --maroon">
  <div class="d-flex align-items-center">
    <a href="../index.php">
      <img src="../resource/logo-uta.png" alt="Logo institucional" style="height: 50px;">
    </a>
    <div class="site-name ms-3 fw-bold">Gestión de Eventos Académicos - FISEI</div>
  </div>
  <div class="d-flex align-items-center gap-3">
    <?php if (isLoggedIn()): ?>
      <span class="fw-semibold">Hola, <?= htmlspecialchars(getUserName()) ?> <?= htmlspecialchars(getUserLastname()) ?></span>
      <a href="../logout.php" class="btn btn-white"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
    <?php else: ?>
      <a href="../login.php" class="btn btn-white"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
      <a href="../registro.php" class="btn btn-white"><i class="fas fa-user-plus"></i> Registrarse</a>
    <?php endif; ?>
  </div>
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
                            <input type="text" id="fecha" name="fecha" readonly value="<?= date('Y-m-d') ?>" />
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
                    <input id="uid" name="uid" type="text" placeholder="ID de usuario" required
                        value="<?= htmlspecialchars($uid) ?>" <?= $sesion_activa ? 'readonly' : '' ?> />
                    <input id="uname" name="uname" type="text" placeholder="Nombre completo" required
                        value="<?= htmlspecialchars($uname) ?>" <?= $sesion_activa ? 'readonly' : '' ?> />
                    <input id="uemail" name="uemail" type="email" placeholder="Correo" required
                        value="<?= htmlspecialchars($uemail) ?>" <?= $sesion_activa ? 'readonly' : '' ?> />
                    <input id="urol" name="urol" type="text" placeholder="Rol" required
                        value="<?= htmlspecialchars($urol) ?>" <?= $sesion_activa ? 'readonly' : '' ?> />
                </fieldset>

            </div>
        </form>
</main>

<footer class="footer-expandido">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
