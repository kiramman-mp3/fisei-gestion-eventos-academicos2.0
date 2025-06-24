<?php
require_once 'session.php';

// Destruir completamente la sesión
session_unset();
session_destroy();

// Redirigir al login u otra página pública
header('Location: index.php');
exit;
