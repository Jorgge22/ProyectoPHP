<?php
session_start();
// Comprobamos si hay idioma en la cookie, si no, usamos español
// Si se solicita idioma por GET, guardamos la cookie y redirigimos a la misma URL sin query
if (isset($_GET['lengua'])) {
    $lang = $_GET['lengua'];
    setcookie('lengua', $lang, time() + 60*60*24*30, '/estructura_base_mvc/forms');
    // actualizar variable de _COOKIE para uso inmediato en esta petición
    $_COOKIE['lengua'] = $lang;
    // redirigir a la misma ruta sin parámetros
    $redir = strtok($_SERVER['REQUEST_URI'], '?');
    header('Location: ' . $redir);
    exit;
}

// Comprobamos si hay idioma en la cookie, si no, usamos español
if (isset($_COOKIE['lengua'])) {
    $lengua = $_COOKIE['lengua'];
} else {
    $lengua = 'es';
}

// Traducciones básicas
if ($lengua === 'en') {
    $T = [
        'login_title' => 'Login',
        'user' => 'User',
        'password' => 'Password',
        'language' => 'Language',
        'login' => 'Login',
        'invalid_credentials' => 'Invalid username or password.'
    ];
} else {
    $T = [
        'login_title' => 'Acceso',
        'user' => 'Usuario',
        'password' => 'Contraseña',
        'language' => 'Idioma',
        'login' => 'Entrar',
        'invalid_credentials' => 'Usuario o contraseña incorrectos.'
    ];
}

// Si hay error guardado en sesión, lo mostramos
$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>

<!doctype html>
<html lang="<?= htmlspecialchars($lengua) ?>">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/estructura_base_mvc/forms/public/css/views/styleLogin.css">
    <title><?= htmlspecialchars($T['login_title']) ?></title>
</head>

<body>
    <div class="login">
        <h2><?= htmlspecialchars($T['login_title']) ?></h2>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="/estructura_base_mvc/forms/models/Login.php" method="post">
            <label><?= $T['user'] ?></label>
            <input type="text" name="usuario" required />

            <label><?= $T['password'] ?></label>
            <input type="password" name="password" required />

            <label><?= $T['language'] ?></label>
            <select name="lengua" onchange="window.location.href = window.location.pathname + '?lengua=' + encodeURIComponent(this.value)">
                <option value="es" <?= $lengua === 'es' ? 'selected' : '' ?>>Español</option>
                <option value="en" <?= $lengua === 'en' ? 'selected' : '' ?>>English</option>
            </select>

            <button type="submit"><?= $T['login'] ?></button>
        </form>
    </div>
</body>
</html>