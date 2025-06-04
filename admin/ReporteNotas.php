<?php
require_once '../session.php';
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
require '../sql/conexion.php';

$result = mysqli_query($conn, "SELECT e.nombre AS evento, est.nombre AS estudiante, i.nota FROM inscritos i JOIN eventos e ON i.id_evento = e.id JOIN estudiantes est ON i.id_estudiante = est.id WHERE e.tipo = 'curso'");
echo "<h2>Reporte de Notas</h2><table border='1'><tr><th>Evento</th><th>Estudiante</th><th>Nota</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr><td>{$row['evento']}</td><td>{$row['estudiante']}</td><td>{$row['nota']}</td></tr>";
}
echo "</table>";
?>