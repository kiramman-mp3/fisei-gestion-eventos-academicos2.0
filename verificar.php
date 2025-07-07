<?php
require_once 'sql/conexion.php';
require_once 'session.php';

$conexion = (new Conexion())->conectar();
$mensaje = '';
$correoValor = $_GET['correo'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $codigo = $_POST['codigo'];

    $stmt = $conexion->prepare("SELECT id, codigo_verificacion, verificado FROM estudiantes WHERE correo = :correo LIMIT 1");
    $stmt->bindValue(':correo', $correo);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);


    if ($usuario) {
        if ($usuario['verificado']) {
            $mensaje = "La cuenta ya fue verificada.";
        } elseif ($usuario['codigo_verificacion'] === $codigo) {
            // Actualizar estado
            $update = $conexion->prepare("UPDATE estudiantes SET verificado = 1, codigo_verificacion = NULL WHERE id = :id");
            $update->bindValue(':id', $usuario['id']);
            $update->execute();

            $mensaje = "Cuenta verificada exitosamente. Ya puedes iniciar sesión.";
        } else {
            $mensaje = "Código incorrecto. Verifica el correo recibido.";
        }
    } else {
        $mensaje = "Correo no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Verificar Código</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 500px;">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Verificación de Cuenta</h3>

                <?php if (!empty($mensaje)): ?>
                    <div class="alert alert-info text-center"><?= htmlspecialchars($mensaje) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" name="correo" required
                            value="<?= htmlspecialchars($correoValor) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código de Verificación</label>
                        <input type="text" class="form-control" name="codigo" required maxlength="10">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Verificar</button>
                    </div>
                </form>

                <div class="mt-3 text-center">
                    <a href="login.php">Volver al inicio de sesión</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>