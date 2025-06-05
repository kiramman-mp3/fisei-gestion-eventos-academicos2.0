<?php
// Simulación de datos
$notas = [
    ["nombre" => "Juan Pérez", "curso" => "PHP Básico", "nota" => 18],
    ["nombre" => "Ana Torres", "curso" => "Java Intermedio", "nota" => 16],
    ["nombre" => "Luis Gómez", "curso" => "Python Avanzado", "nota" => 20],
    ["nombre" => "María Rodríguez", "curso" => "HTML y CSS", "nota" => 17],
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Reporte de Notas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <div class="container my-5">
    <h2 class="mb-4 text-center">Reporte de Notas</h2>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>Nombre</th>
          <th>Curso</th>
          <th>Nota</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($notas as $nota): ?>
          <tr>
            <td><?= $nota["nombre"] ?></td>
            <td><?= $nota["curso"] ?></td>
            <td><?= $nota["nota"] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="d-flex justify-content-between mt-4">
      <a href="dashboard.html" class="btn btn-secondary">← Volver al Dashboard</a>
      <a href="generar_pdf_notas.php" class="btn btn-danger">Descargar PDF</a>
    </div>
  </div>
</body>
</html>
