<?php
include 'sql/conexion.php';
$conn = (new Conexion())->conectar();
$stmt = $conn->query("SELECT tipo, contenido FROM info_fisei");
$data = [];
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $val = in_array($r['tipo'], ['autoridad', 'resena', 'carrusel', 'nosotros']) ? json_decode($r['contenido'], true) : $r['contenido'];
    $data[$r['tipo']][] = $val;
}
?><!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Inicio - FISEI</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <header class="ctt-header">
        <div class="top-bar">
            <div class="logo">
                <img src="resources/logo.png" alt="Logo FISEI">
            </div>
            <div class="top-links">
                <div class="link-box">
                    <i class="fas fa-desktop"></i>
                    <div>
                        <span class="title">Plataforma Educativa</span><br>
                        <a href="#">Ingresa aquí</a>
                    </div>
                </div>
            </div>
        </div>
        <nav class="main-nav">
            <div class="menu-icon">
                <i class="fas fa-bars"></i>
            </div>
            <ul class="menu">
                <li><a href="#">Inicio</a></li>
                <li><a href="#">Nosotros</a></li>
                <li><a href="#">Cursos</a></li>
                <li><a href="#">Contáctanos</a></li>
            </ul>
        </nav>
    </header>

    <section class="carousel">
        <div class="slides">
            <?php foreach ($data['carrusel'] ?? [] as $index => $c): ?>
                <div class="slide<?= $index === 0 ? ' active' : '' ?>">
                    <img src="<?= $c['img'] ?>" alt="">
                    <div class="overlay">
                        <h1><?= htmlspecialchars($c['titulo']) ?></h1>
                        <p><?= htmlspecialchars($c['descripcion']) ?></p>
                        <a href="ver_cursos.php?index=<?= $index ?>" class="boton">Ver más</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="carousel-controls">
            <button onclick="prevSlide()">&#10094;</button>
            <button onclick="nextSlide()">&#10095;</button>
        </div>

        <div class="carousel-indicators">
            <?php foreach ($data['carrusel'] ?? [] as $i => $c): ?>
                <span class="indicador-circular<?= $i === 0 ? ' active' : '' ?>" onclick="goToSlide(<?= $i ?>)"></span>
            <?php endforeach; ?>
        </div>
    </section>

    <?php foreach ($data['nosotros'] ?? [] as $item): ?>
        <section class="nosotros seccion">
            <div class="nosotros-container">
                <div class="nosotros-img">
                    <img src="<?= htmlspecialchars($item['img']) ?>" alt="Imagen Nosotros">
                </div>
                <div class="nosotros-texto">
                    <span class="etiqueta">SOBRE NOSOTROS</span>
                    <h2><?= htmlspecialchars($item['titulo']) ?></h2>
                    <p><?= htmlspecialchars($item['descripcion']) ?></p>
                    <a href="#" class="boton">Ver más</a>
                </div>
            </div>
        </section>
    <?php endforeach; ?>

    <section class="autoridades seccion">
        <div class="autoridades-header">
            <span class="etiqueta">FISEI</span>
            <h2>Facultad de Ingeniería en Sistemas, Electrónica e Industrial</h2>
        </div>
        <div class="autoridades-grid">
            <?php foreach ($data['autoridad'] ?? [] as $a): ?>
                <div class="autoridad-card">
                    <img src="<?= htmlspecialchars($a['img']) ?>" alt="Foto de <?= htmlspecialchars($a['nombre']) ?>">
                    <div class="autoridad-info">
                        <h3><?= htmlspecialchars($a['nombre']) ?></h3>
                        <p><?= htmlspecialchars($a['cargo']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="resenas seccion">
        <div class="resenas-header">
            <span class="etiqueta">RESEÑAS</span>
            <h2>OPINIONES</h2>
        </div>

        <div class="resena-slider">
            <?php foreach ($data['resena'] ?? [] as $i => $r): ?>
                <div class="resena-slide<?= $i === 0 ? ' active' : '' ?>">
                    <i class="fas fa-quote-left quote-icon"></i>
                    <p class="resena-texto"><?= htmlspecialchars($r['texto']) ?></p>
                    <div class="resena-persona">
                        <img src="<?= htmlspecialchars($r['img']) ?>" alt="Foto de <?= htmlspecialchars($r['autor']) ?>">
                        <div>
                            <strong><?= htmlspecialchars($r['autor']) ?></strong><br>
                            <span><?= htmlspecialchars($r['rol']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="resena-indicadores">
            <?php foreach ($data['resena'] ?? [] as $i => $r): ?>
                <span class="indicador-circular<?= $i === 0 ? ' active' : '' ?>" onclick="cambiarResena(<?= $i ?>)"></span>
            <?php endforeach; ?>
        </div>
    </section>

    <script>
        // CARRUSEL
        let slideIndex = 0;
        const slides = document.querySelectorAll('.slide');
        const indicators = document.querySelectorAll('.carousel-indicators .indicador-circular');

        function showSlide(index) {
            slides.forEach((s, i) => s.classList.toggle('active', i === index));
            indicators.forEach((dot, i) => dot.classList.toggle('active', i === index));
            slideIndex = index;
        }

        function nextSlide() {
            showSlide((slideIndex + 1) % slides.length);
        }

        function prevSlide() {
            showSlide((slideIndex - 1 + slides.length) % slides.length);
        }

        function goToSlide(index) {
            showSlide(index);
        }

        setInterval(nextSlide, 7000);

        // RESEÑAS
        let resenaIndex = 0;
        const resenaSlides = document.querySelectorAll('.resena-slide');
        const resenaDots = document.querySelectorAll('.resena-indicadores .indicador-circular');

        function mostrarResena(index) {
            resenaSlides.forEach((s, i) => s.classList.toggle('active', i === index));
            resenaDots.forEach((d, i) => d.classList.toggle('active', i === index));
            resenaIndex = index;
        }

        function cambiarResena(index) {
            mostrarResena(index);
        }

        setInterval(() => {
            resenaIndex = (resenaIndex + 1) % resenaSlides.length;
            mostrarResena(resenaIndex);
        }, 7000);
    </script>

</body>

</html>