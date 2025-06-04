<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reporte de Asistencia - UTA</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/styles.css" />
</head>
<body>

  <header class="text-center bg-danger text-white py-4 shadow-sm">
    <h1 class="mb-0">Reporte de Asistencia</h1>
    <p class="mb-0">Universidad Técnica de Ambato</p>
  </header>

  <main class="container py-5">
    <div class="reporte-container">
      <h2 class="mb-4">Asistencias Registradas</h2>

      <!-- Ejemplo de tabla de asistencia -->
      <table class="table table-bordered table-hover table-custom">
        <thead>
          <tr>
            <th>Estudiante</th>
            <th>Curso</th>
            <th>Fecha</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Juan Pérez</td>
            <td>Curso de Java</td>
            <td>2025-06-01</td>
            <td>Presente</td>
          </tr>
          <tr>
            <td>María Gómez</td>
            <td>Curso de PHP</td>
            <td>2025-06-01</td>
            <td>Ausente</td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>

  <footer class="text-center text-white bg-danger py-3 mt-5">
    <small>&copy; 2025 Universidad Técnica de Ambato – Todos los derechos reservados</small>
  </footer>

</body>
</html>
