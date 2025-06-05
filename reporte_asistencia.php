<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reporte de Asistencia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

  <header class="text-center bg-primary text-white py-4 shadow-sm">
    <h1 class="mb-0">Reporte de Asistencia</h1>
    <p class="mb-0">Universidad Técnica de Ambato</p>
  </header>

  <main class="container py-5">
    <!-- Botones de acción -->
    <div class="mb-4 d-flex justify-content-between">
      <a href="dashboard.html" class="btn btn-secondary">← Volver al Dashboard</a>
      <a href="generar_pdf_asistencia.php" class="btn btn-danger">Descargar PDF</a>
    </div>

    <!-- Tabla de asistencia -->
    <h4>Asistencia por Curso</h4>
    <table class="table table-bordered table-striped mt-3">
      <thead class="table-primary">
        <tr>
          <th>Nombre del Estudiante</th>
          <th>Curso</th>
          <th>Fecha</th>
          <th>Asistencia</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Andrea López</td>
          <td>Python Básico</td>
          <td>2025-06-03</td>
          <td>Presente</td>
        </tr>
        <tr>
          <td>Carlos Pérez</td>
          <td>Python Básico</td>
          <td>2025-06-03</td>
          <td>Ausente</td>
        </tr>
        <tr>
          <td>Valeria Ruiz</td>
          <td>Java Avanzado</td>
          <td>2025-06-03</td>
          <td>Presente</td>
        </tr>
      </tbody>
    </table>
  </main>

  <footer class="text-center text-white bg-primary py-3 mt-5">
    <small>&copy; 2025 Universidad Técnica de Ambato – Todos los derechos reservados</small>
  </footer>

</body>
</html>
