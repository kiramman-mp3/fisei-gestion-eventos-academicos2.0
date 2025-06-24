# Sistema de Gestión de Eventos Académicos - FISEI

## Correcciones de Rutas Realizadas

### Fecha de corrección: 23 de Junio de 2025

### Archivos corregidos:

#### 1. Directorio `informativo/`
- **que_es_eventos.php**: Corregido require_once y enlaces del header
- **manual_usuario.php**: Corregido require_once y enlaces del header  
- **nosotros.php**: Corregido require_once y enlaces del header
- **preguntas_frecuentes.php**: Corregido require_once y enlaces del header
- **versiones.php**: Corregido require_once y enlaces del header

#### 2. Directorio `legal/`
- **politica_privacidad.php**: Corregido enlace al index en el header
- **terminos_uso.php**: Corregido enlace al index en el header
- **licencia.php**: Corregido enlace al index en el header

#### 3. Archivos principales
- **registro.php**: Corregidos múltiples enlaces del footer y header
- **perfil.php**: Corregidos enlaces del footer
- **ver_cursos.php**: Corregido fetch de API y enlaces del sidebar

#### 4. Directorio `admin/`
- **administrar_evento.php**: Corregido header de redirección
- **solicitudes_admin.php**: Corregido enlace al logo
- **ver_resoluciones.php**: Corregido enlace al logo

#### 5. Directorio `estudiantes/`
- **generar_certificado.php**: Corregida ruta de redirección crítica

### Principales problemas corregidos:

1. **Rutas relativas incorrectas**: Se corrigieron rutas que apuntaban a directorios inexistentes
2. **Enlaces rotos**: Se actualizaron enlaces del header y footer en múltiples archivos
3. **Error de ortografía**: Se corrigió "solictud_cambios.php" por "solicitud_cambios.php" en todos los archivos
4. **Rutas de API**: Se actualizó la referencia de "CursosPorCarrera.php" a "cursosPorCarrera.php"
5. **Redirecciones**: Se corrigieron redirecciones con rutas incorrectas como "../../login.php"

### Archivos que requieren verificación adicional:

1. **dashboard.html**: Revisar enlaces del sidebar a archivos que pueden no existir
2. **Archivos de formulario**: Verificar que todas las rutas de action apunten correctamente
3. **JavaScript**: Revisar rutas en archivos JS para peticiones AJAX

### Recomendaciones:

1. **Implementar rutas absolutas**: Considerar usar rutas absolutas desde la raíz del proyecto
2. **Crear constantes**: Definir constantes para rutas comunes en un archivo de configuración
3. **Validación de archivos**: Implementar verificación de existencia de archivos antes de incluirlos
4. **Testing**: Realizar pruebas de navegación completas en todos los módulos

### Estructura de rutas estándar aplicada:

- Desde archivos en subdirectorios: `../` para subir un nivel
- Enlaces al index principal: `../index.php` desde subdirectorios
- Recursos estáticos: `../resource/` desde subdirectorios
- Archivos CSS: `../css/` desde subdirectorios
- APIs de servicios: `../service/` desde subdirectorios

### Pasos recomendados:

1. Probar toda la navegación del sitio
2. Verificar que las imágenes se cargan correctamente
3. Comprobar que los formularios envían datos a las rutas correctas
4. Validar que las APIs responden correctamente
5. Realizar pruebas de autenticación y redirecciones

---

**Nota**: Este documento debe actualizarse cada vez que se realicen cambios en la estructura de archivos o rutas del proyecto.
