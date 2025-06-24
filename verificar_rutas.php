<?php
/**
 * Script de verificación de rutas - FISEI Gestión de Eventos
 * Este archivo verifica que las rutas críticas existan y sean accesibles
 */

echo "<h1>Verificación de Rutas del Sistema</h1>";
echo "<h2>Fecha: " . date('Y-m-d H:i:s') . "</h2>";

// Rutas críticas a verificar
$rutas_criticas = [
    // Archivos principales
    'index.php' => 'Página principal',
    'login.php' => 'Página de login',
    'registro.php' => 'Página de registro',
    'perfil.php' => 'Página de perfil',
    'ver_cursos.php' => 'Página de cursos',
    'dashboard.html' => 'Dashboard administrativo',
    
    // Directorio SQL
    'sql/conexion.php' => 'Conexión a base de datos',
    
    // Directorio CSS
    'css/styles.css' => 'Estilos principales',
    'css/estilos.css' => 'Estilos secundarios',
    
    // Directorio informativo
    'informativo/que_es_eventos.php' => '¿Qué es Eventos?',
    'informativo/manual_usuario.php' => 'Manual de usuario',
    'informativo/nosotros.php' => 'Nosotros',
    'informativo/preguntas_frecuentes.php' => 'Preguntas frecuentes',
    'informativo/versiones.php' => 'Versiones',
    
    // Directorio legal
    'legal/politica_privacidad.php' => 'Política de privacidad',
    'legal/terminos_uso.php' => 'Términos de uso',
    'legal/licencia.php' => 'Licencia',
    
    // Directorio formulario
    'formulario/solicitud_cambios.php' => 'Solicitud de cambios',
    'formulario/solicitar_ayuda.php' => 'Solicitar ayuda',
    'formulario/guardar_solicitud.php' => 'Guardar solicitud',
    
    // Directorio admin
    'admin/administrar_evento.php' => 'Administrar evento',
    'admin/solicitudes_admin.php' => 'Solicitudes admin',
    'admin/ver_resoluciones.php' => 'Ver resoluciones',
    'admin/comprobantes_pendientes.php' => 'Comprobantes pendientes',
    
    // Directorio estudiantes
    'estudiantes/mis_cursos.php' => 'Mis cursos',
    'estudiantes/generar_certificado.php' => 'Generar certificado',
    'estudiantes/subir_comprobante.php' => 'Subir comprobante',
    
    // Directorio service
    'service/curso.php' => 'API Cursos',
    'service/cursosPorCarrera.php' => 'API Cursos por carrera',
    'service/categoria_evento.php' => 'API Categorías',
    'service/tipo_evento.php' => 'API Tipos de evento',
    
    // Recursos
    'resource/logo-universidad-tecnica-de-ambato.webp' => 'Logo UTA',
    'resource/placeholder.svg' => 'Imagen placeholder',
    
    // Librerías
    'libs/fpdf/fpdf.php' => 'Librería PDF',
];

$errores = [];
$exitosos = 0;

echo "<table border='1' cellpadding='10' cellspacing='0' style='width: 100%; border-collapse: collapse;'>";
echo "<tr style='background-color: #f0f0f0;'>";
echo "<th>Archivo</th><th>Descripción</th><th>Estado</th><th>Observaciones</th>";
echo "</tr>";

foreach ($rutas_criticas as $ruta => $descripcion) {
    $ruta_completa = __DIR__ . '/' . $ruta;
    $existe = file_exists($ruta_completa);
    
    echo "<tr>";
    echo "<td><strong>$ruta</strong></td>";
    echo "<td>$descripcion</td>";
    
    if ($existe) {
        echo "<td style='color: green; font-weight: bold;'>✓ EXISTE</td>";
        echo "<td>Archivo encontrado correctamente</td>";
        $exitosos++;
    } else {
        echo "<td style='color: red; font-weight: bold;'>✗ NO EXISTE</td>";
        echo "<td>Archivo no encontrado - verificar ruta</td>";
        $errores[] = $ruta;
    }
    
    echo "</tr>";
}

echo "</table>";


echo "<h2>Resumen de Verificación</h2>";
echo "<p><strong>Total de archivos verificados:</strong> " . count($rutas_criticas) . "</p>";
echo "<p><strong>Archivos encontrados:</strong> <span style='color: green;'>$exitosos</span></p>";
echo "<p><strong>Archivos no encontrados:</strong> <span style='color: red;'>" . count($errores) . "</span></p>";

if (count($errores) > 0) {
    echo "<h3>Archivos que requieren atención:</h3>";
    echo "<ul>";
    foreach ($errores as $error) {
        echo "<li style='color: red;'>$error</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: green; font-weight: bold; font-size: 18px;'>🎉 ¡Todas las rutas críticas están correctamente configuradas!</p>";
}

echo "<hr>";
echo "<p><em>Verificación completada el " . date('Y-m-d H:i:s') . "</em></p>";
?>
