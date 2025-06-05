<?php
// Simulación de datos
$pagos = [
    ["nombre" => "Juan Pérez", "curso" => "PHP Básico", "monto" => 100.00, "fecha" => "2025-05-10"],
    ["nombre" => "Ana Torres", "curso" => "Java Intermedio", "monto" => 120.00, "fecha" => "2025-05-12"],
    ["nombre" => "Luis Gómez", "curso" => "Python Avanzado", "monto" => 150.00, "fecha" => "2025-05-14"],
    ["nombre" => "María Rodríguez", "curso" => "HTML y CSS", "monto" => 90.00, "fecha" => "2025-05-15"],
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Reporte de Pagos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <div class="container my-5">
    <h2 class="mb-4 text-center">Reporte de Pagos</h2>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>Nombre</th>
          <th>Curso</th>
          <th>Monto</th>
          <th>Fecha</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pagos as $pago): ?>
          <tr>
            <td><?= $pago["nombre"] ?></td>
            <td><?= $pago["curso"] ?></td>
            <td>$<?= number_format($pago["monto"], 2) ?></td>
            <td><?= $pago["fecha"] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="d-flex justify-content-between mt-4">
      <a href="dashboard.php" class="btn btn-secondary">← Volver al Dashboard</a>
      <a href="generar_pdf_pagos.php" class="btn btn-danger">Descargar PDF</a>
    </div>
  </div>
</body>
</html>
