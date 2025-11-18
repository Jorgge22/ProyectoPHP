<?php
session_start();

// Cargar traducciones 
require_once __DIR__ . '/Idioma.php';

// Leer lengua POST > GET > cookie 
$lengua = 'es';
if (isset($_POST['lengua'])) $lengua = $_POST['lengua'];
elseif (isset($_GET['lengua'])) $lengua = $_GET['lengua'];
elseif (isset($_COOKIE['lengua'])) $lengua = $_COOKIE['lengua'];

$T = traducir($lengua);

// Recoger datos del formulario
$user = $_POST['usuario'] ?? '';
$pass = $_POST['password'] ?? '';

// Credenciales correctas
$USER_OK = 'daw';
$PASS_OK = 'daw2025';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($user === $USER_OK && $pass === $PASS_OK) {
        // Login correcto: crear sesión y guardar idioma en cookie
        $_SESSION['logged'] = true;
        $_SESSION['user'] = $user;
        $_SESSION['login_time'] = time(); // marcar momento del login
        setcookie('lengua', $lengua, time() + 30*24*3600, '/'); // 30 días

        // Redirigir al listado 
        header('Location: Model.php');
        exit;
    } else {
        // Login incorrecto: guardar mensaje en sesión y volver al formulario
        $_SESSION['login_error'] = $T['invalid_credentials'] ?? 'Usuario o contraseña incorrectos.';
        header('Location: ../views/inicio.php');
        exit;
    }
}

// Si se accede por GET, redirigir al login
header('Location: ../views/inicio.php');
exit;
