<?php
session_start();

// Si se solicita cambiar idioma desde el listado (GET), guardamos la cookie
if (isset($_GET['lengua'])) {
    setcookie('lengua', $_GET['lengua'], time() + 30 * 24 * 3600, '/');
    // actualizamos $_COOKIE para que la página actual refleje el cambio sin recarga extra
    $_COOKIE['lengua'] = $_GET['lengua'];
}

// Comprobamos caducidad de 2 minutos
if (!isset($_SESSION['logged']) || !isset($_SESSION['login_time']) || (time() - $_SESSION['login_time'] > 120)) {
    session_unset();
    session_destroy();
    header('Location: ../views/inicio.php');
    exit;
}

// Cargar traducciones 
require_once __DIR__ . '/Idioma.php';
if (isset($_COOKIE['lengua'])) {
    $lengua = $_COOKIE['lengua'];
} else {
    $lengua = 'es';
}
$T = traducir($lengua);

// Mapas de traducción para mostrar tipo y estado en inglés cuando 
$map_tipo = [
    'Proyecto interno' => ['es' => 'Proyecto interno', 'en' => 'Internal project'],
    'Consultoria' => ['es' => 'Consultoria', 'en' => 'Consultancy'],
    'Iniciativa RRHH' => ['es' => 'Iniciativa RRHH', 'en' => 'HR initiative']
];

$map_estado = [
    'En progreso' => ['es' => 'En progreso', 'en' => 'In progress'],
    'Bloqueado'   => ['es' => 'Bloqueado',   'en' => 'Blocked'],
    'Finalizado'  => ['es' => 'Finalizado',  'en' => 'Finished'],
    'Pendiente'   => ['es' => 'Pendiente',   'en' => 'Pending']
];


require_once __DIR__ . '/Proyecto.php';
$proyectoModel = new Proyecto();

// recogemos los filtros que se mandan por metodo get
$nombreFilter = isset($_GET['nombre']) ? trim($_GET['nombre']) : '';
$tipoFilter = isset($_GET['tipo']) ? trim($_GET['tipo']) : '';
$estadoFilter = isset($_GET['estado']) ? trim($_GET['estado']) : '';
$tecFilter = isset($_GET['tecnologia']) ? trim($_GET['tecnologia']) : '';

// Obtener todos los proyectos
$todos = $proyectoModel->obtenerTodos();
$resultados = [];
foreach ($todos as $proyecto) {
    // comprobar nombre 
    if ($nombreFilter !== '') {
        if (stripos($proyecto['nombre'], $nombreFilter) === false) {
            continue;
        }
    }
    // comprobar tipo
    if ($tipoFilter !== '') {
        if ($proyecto['tipo'] !== $tipoFilter) {
            continue;
        }
    }
    // comprobar estado
    if ($estadoFilter !== '') {
        if ($proyecto['estado'] !== $estadoFilter) {
            continue;
        }
    }
    // comprobar tecnología
    if ($tecFilter !== '') {
        if (strpos($proyecto['tecnologias'], $tecFilter) === false) {
            continue;
        }
    }
    $resultados[] = $proyecto;
}


// Obtener tipos, estados y tecnologías desde la base de datos
require_once __DIR__ . '/../lib/Database.php';
$db = (new Database())->pdo;
$tipos = $db->query('SELECT nombre FROM tipoProyecto')->fetchAll(PDO::FETCH_COLUMN);
$estados = $db->query('SELECT nombre FROM estadoProyecto')->fetchAll(PDO::FETCH_COLUMN);
$tecnologias = $db->query('SELECT nombre FROM tecnologia')->fetchAll(PDO::FETCH_COLUMN);

// funcion para mostrar cada proyecto en formato html 
function mostrarProyecto($proyecto, $lengua, $map_tipo, $map_estado, $T)
{
    // traducir tipo y estado si existe en los mapas
    $tipo = isset($map_tipo[$proyecto['tipo']]) ? $map_tipo[$proyecto['tipo']][$lengua] : $proyecto['tipo'];
    $estado = isset($map_estado[$proyecto['estado']]) ? $map_estado[$proyecto['estado']][$lengua] : $proyecto['estado'];

    $html = '<div class="proyecto" style="border:1px solid #ddd;padding:8px;margin:8px 0;">';
    $html .= '<strong>' . htmlspecialchars($proyecto['nombre']) . '</strong><br>';
    $html .= '<small><em>' . htmlspecialchars($tipo) . ' — ' . htmlspecialchars($estado) . '</em></small>';
    $html .= '<p>' . htmlspecialchars($proyecto['descripcion']) . '</p>';
    $html .= '<p><strong>' . $T['technology'] . ':</strong> ' . htmlspecialchars($proyecto['tecnologias']) . '</p>';
    $html .= '</div>';
    return $html;
}


// contamos cuantos proyectos se muestran
$totalMostrados = count($resultados);
?>
<!doctype html>
<html lang="<?php echo $lengua ?>">

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/estructura_base_mvc/forms/public/css/style.css">
    <title><?php echo $T['title'] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f6f7f8;
        }

        .filtros {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .filtros div {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 4px;
        }

        input,
        select {
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            background: #007bff;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background: #0069d9;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .lang-form {
            display: inline-block;
        }
    </style>
</head>

<body>
    <div class="top-bar">
        <h1><?php echo $T['title'] ?></h1>

        <!-- selector de idioma: al cambiar envía por GET y guarda cookie -->
        <form class="lang-form" method="get">
            <label><?php echo $T['language'] ?>:</label>
            <select name="lengua" onchange="this.form.submit()">
                <option value="es" <?php echo $lengua === 'es' ? 'selected' : '' ?>>Español</option>
                <option value="en" <?php echo $lengua === 'en' ? 'selected' : '' ?>>English</option>
            </select>
        </form>
    </div>

    <!-- formulario con los filtros, se envia por get -->
    <form method="get" class="filtros">
        <div>
            <label><?php echo $T['name_or_desc'] ?></label>
            <input type="text" name="nombre" value="<?php echo $nombreFilter ?>" placeholder="Buscar..." />
        </div>

        <div>
            <label><?php echo $T['type'] ?></label>
            <select name="tipo">
                <option value=""><?php echo $T['all'] ?></option>
                <?php foreach ($tipos as $t): ?>
                    <option value="<?php echo $t ?>" <?php echo $t === $tipoFilter ? 'selected' : '' ?>><?php echo $t ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label><?php echo $T['status'] ?></label>
            <select name="estado">
                <option value=""><?php echo $T['all'] ?></option>
                <?php foreach ($estados as $e): ?>
                    <option value="<?php echo $e ?>" <?php echo $e === $estadoFilter ? 'selected' : '' ?>><?php echo $e ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label><?php echo $T['technology'] ?></label>
            <select name="tecnologia">
                <option value=""><?php echo $T['all'] ?></option>
                <?php foreach ($tecnologias as $t): ?>
                    <option value="<?php echo htmlspecialchars($t) ?>" <?php echo (is_array($tecFilter) ? (in_array($t, $tecFilter) ? 'selected' : '') : ($t === $tecFilter ? 'selected' : '')) ?>><?php echo htmlspecialchars($t) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit"><?php echo $T['filter'] ?></button>
    </form>

    <!-- aqui se muestran los resultados filtrados -->
    <h2><?php echo $T['projects_list'] ?></h2>

    <?php if ($totalMostrados === 0): ?>
        <p><?php echo $T['no_results'] ?></p>
    <?php else: ?>
        <p><?php echo $T['total_shown'] ?>: <strong><?php echo $totalMostrados ?></strong></p>
        <?php foreach ($resultados as $p): ?>
            <?php echo mostrarProyecto($p, $lengua, $map_tipo, $map_estado, $T) ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="seleccion-accion">
        <a href="/estructura_base_mvc/forms/proyectos">
            <button type="button">Crear Nuevo Proyecto</button>
        </a>
    </div>

</body>

</html>