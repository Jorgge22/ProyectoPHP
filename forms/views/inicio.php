<?php
session_start();

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
    <title><?= htmlspecialchars($T['login_title']) ?></title>
    <style>
        body {
            font-family: Arial;
            margin: 40px;
            background: #f6f7f8
        }

        .login {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 360px
        }

        label {
            display: block;
            margin-top: 8px;
            font-weight: bold
        }

        input,
        select {
            width: 100%;
            padding: 8px;
            margin-top: 4px
        }

        button {
            margin-top: 12px;
            padding: 8px 12px
        }

        .error {
            color: #c00;
            margin-top: 8px
        }
    </style>
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
            <select name="lengua">
                <option value="es" <?= $lengua === 'es' ? 'selected' : '' ?>>Español</option>
                <option value="en" <?= $lengua === 'en' ? 'selected' : '' ?>>English</option>
            </select>

            <button type="submit"><?= $T['login'] ?></button>
        </form>
    </div>
</body>

</html>