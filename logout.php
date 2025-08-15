<?php
session_start();

// Vacía la sesión
$_SESSION = [];

// Borra cookie de sesión (por si acaso)
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

// Destruye y vuelve al inicio
session_destroy();
header('Location: index.php?msg=logout');
exit;