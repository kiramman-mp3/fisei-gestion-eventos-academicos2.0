<?php
// includes/session.php

// Inicia la sesión si aún no se ha iniciado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =====================
// Funciones auxiliares
// =====================

function getUserId() {
    return $_SESSION['usuario_id'] ?? null;
}

function getUserEmail() {
    return $_SESSION['email'] ?? null;
}

function getUserName() {
    return $_SESSION['nombre'] ?? null;
}

function getUserLastname() {
    return $_SESSION['apellido'] ?? null;
}

function getUserRole() {
    return $_SESSION['rol'] ?? null;
}

function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}
