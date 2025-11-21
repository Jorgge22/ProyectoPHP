<?php
// ...existing code...
session_start();

// 1. Manejar borrado directo (si se llamó ?borrar=ID)
if (isset($_GET['borrar'])) {
    // Es crucial que Database.php esté correcto y la conexión funcione
    require_once __DIR__ . '/../lib/Database.php';
    $db = (new Database())->pdo;
    $id = (int) $_GET['borrar'];

    if ($id > 0) {
        // Iniciar transacción para asegurar la integridad
        $db->beginTransaction();
        try {
            // Eliminar relaciones (si no hay ON DELETE CASCADE)
            $stmt = $db->prepare('DELETE FROM proyecto_tecnologia WHERE id_proyecto = ?');
            $stmt->execute([$id]);

            // Eliminar proyecto
            $stmt = $db->prepare('DELETE FROM proyecto WHERE id = ?');
            $stmt->execute([$id]);

            $db->commit();
            // ¡El borrado debería funcionar aquí!

        } catch (PDOException $e) {
            $db->rollBack();
            // Opcional: registrar el error o mostrar un mensaje al usuario
            // echo "Error al intentar borrar: " . $e->getMessage();
        }
    }
    // Redirigir al listado (ruta verificada):
    header('Location: /estructura_base_mvc/forms/proyectos');
    exit;
}

// Si se solicita cambiar idioma desde el listado (GET), guardamos la cookie
if (isset($_GET['lengua'])) {
    setcookie('lengua', $_GET['lengua'], time() + 30 * 24 * 3600, '/');
    $_COOKIE['lengua'] = $_GET['lengua'];
}

// Comprobamos caducidad de 2 minutos
if (!isset($_SESSION['logged']) || !isset($_SESSION['login_time']) || (time() - $_SESSION['login_time'] > 120)) {
    session_unset();
    session_destroy();
    header('Location: ../views/inicio.php');
    exit;
}

require_once __DIR__ . '/Idioma.php';
$lengua = $_COOKIE['lengua'] ?? 'es';
$T = traducir($lengua);

require_once __DIR__ . '/Proyecto.php';
$proyectoModel = new Proyecto();

// recogemos los filtros que se mandan por metodo GET
$nombreFilter = isset($_GET['nombre']) ? trim($_GET['nombre']) : '';
$tipoFilter = isset($_GET['tipo']) ? trim($_GET['tipo']) : '';
$estadoFilter = isset($_GET['estado']) ? trim($_GET['estado']) : '';

// aceptar múltiples tecnologías: name="tecnologia[]" -> $_GET['tecnologia'] es array
$tecFilter = isset($_GET['tecnologia']) ? (array) $_GET['tecnologia'] : [];

// ** FILTRO TECNOLOGÍAS - CORRECCIÓN VERIFICADA:**
// Eliminar las cadenas vacías (de la opción 'Todos') para que 'empty' funcione correctamente.
$tecFilter = array_filter(array_map('trim', $tecFilter));

// Obtener todos los proyectos (el modelo devuelve los datos, no pinta)
$todos = $proyectoModel->obtenerTodos();
$resultados = [];
foreach ($todos as $proyecto) {
    // comprobar nombre 
    if ($nombreFilter !== '') {
        // stristr o stripos son mejores para búsqueda insensible a mayúsculas
        if (stripos($proyecto['nombre'], $nombreFilter) === false && stripos($proyecto['descripcion'] ?? '', $nombreFilter) === false) {
            continue; // Se añade búsqueda en descripción para ser más útil
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
    // comprobar tecnologías: filtra por MÚLTIPLES elementos (o uno solo)
    if (!empty($tecFilter)) {
        // normalizar tecnologías del proyecto a array
        if (is_array($proyecto['tecnologias'])) {
            $projTechs = $proyecto['tecnologias'];
        } else {
            $projTechs = array_map('trim', explode(',', $proyecto['tecnologias'] ?? ''));
        }

        $match = false;
        foreach ($tecFilter as $tf) {
            foreach ($projTechs as $pt) {
                if (strcasecmp($pt, $tf) === 0) {
                    $match = true;
                    break 2;
                }
            }
        }
        if (!$match) continue;
    }

    $resultados[] = $proyecto;
}

// Obtener tipos, estados y tecnologías desde la base de datos (para los selects)
require_once __DIR__ . '/../lib/Database.php';
$db = (new Database())->pdo;
$tipos = $db->query('SELECT nombre FROM tipoProyecto')->fetchAll(PDO::FETCH_COLUMN);
$estados = $db->query('SELECT nombre FROM estadoProyecto')->fetchAll(PDO::FETCH_COLUMN);
$tecnologias = $db->query('SELECT nombre FROM tecnologia')->fetchAll(PDO::FETCH_COLUMN);

// ...existing code...
// Añadir maps para evitar warnings si no existen traducciones/external maps
$map_tipo = [];
foreach ($tipos as $t) {
    // mapeo simple: misma etiqueta en ES/EN (fallback)
    $map_tipo[$t] = ['es' => $t, 'en' => $t];
}

$map_estado = [];
foreach ($estados as $e) {
    $map_estado[$e] = ['es' => $e, 'en' => $e];
}

// ** La función 'mostrarTecnologias' sí existe y se utiliza correctamente **
function mostrarTecnologias($tecnologias)
{
    if (is_array($tecnologias)) {
        $list = $tecnologias;
    } else {
        $list = array_filter(array_map('trim', explode(',', (string)$tecnologias)));
    }
    $out = [];
    foreach ($list as $t) {
        $out[] = '<span class="tag">' . htmlspecialchars($t) . '</span>';
    }
    return implode(' ', $out);
}

// ** La función 'mostrarProyecto' ha sido renombrada a 'mostrarProyectos' **
function mostrarProyectos(array $lista, $lengua, $map_tipo, $map_estado, $T)
{
    $html = '';
    foreach ($lista as $proyecto) {
        $tipo = isset($map_tipo[$proyecto['tipo']]) ? $map_tipo[$proyecto['tipo']][$lengua] : $proyecto['tipo'];
        $estado = isset($map_estado[$proyecto['estado']]) ? $map_estado[$proyecto['estado']][$lengua] : $proyecto['estado'];

        $html .= '<div class="proyecto card" style="margin-bottom:12px;">';
        $html .= '<div style="display:flex;justify-content:space-between;align-items:center">';
        $html .= '<strong>' . htmlspecialchars($proyecto['nombre']) . '</strong>';
        $html .= '<div class="acciones">';
        $html .= '<button type="button" class="btn ghost" onclick="window.location.href=\'/estructura_base_mvc/forms/proyectos/modificar?id=' . urlencode($proyecto['id']) . '\'">Modificar</button> ';
        // Se asegura que el borrado apunta a la URL del listado con el parámetro 'borrar'
        $html .= '<button type="button" class="btn ghost danger" onclick="if(confirm(\'Borrar proyecto?\')) window.location.href=\'/estructura_base_mvc/forms/proyectos/borrar?id=' . urlencode($proyecto['id']) . '\'">Borrar</button>';
        $html .= '</div></div>';
        $html .= '<small><em>' . htmlspecialchars($tipo) . ' — ' . htmlspecialchars($estado) . '</em></small>';
        $html .= '<p>' . htmlspecialchars($proyecto['descripcion']) . '</p>';
        $html .= '<p><strong>' . $T['technology'] . ':</strong> ' . mostrarTecnologias($proyecto['tecnologias']) . '</p>';
        $html .= '</div>';
    }
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
        /* estilos mínimos para las tags de tecnologías */
        .tag {
            display: inline-block;
            background: #eef6ff;
            color: #0b63d6;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-right: 6px;
        }
    </style>
</head>

<body>
    <div class="top-bar">
        <h1><?php echo $T['title'] ?></h1>
        <form class="lang-form" method="get">
            <label><?php echo $T['language'] ?>:</label>
            <select name="lengua" onchange="window.location.href = window.location.pathname + '?lengua=' + encodeURIComponent(this.value)">
                <option value="es" <?php echo $lengua === 'es' ? 'selected' : '' ?>>Español</option>
                <option value="en" <?php echo $lengua === 'en' ? 'selected' : '' ?>>English</option>
            </select>
        </form>
    </div>

    <form method="get" class="filtros">
        <div>
            <label><?php echo $T['name_or_desc'] ?></label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($nombreFilter) ?>" placeholder="Buscar..." />
        </div>

        <div>
            <label><?php echo $T['type'] ?></label>
            <select name="tipo">
                <option value=""><?php echo $T['all'] ?></option>
                <?php foreach ($tipos as $t): ?>
                    <option value="<?php echo htmlspecialchars($t) ?>" <?php echo $t === $tipoFilter ? 'selected' : '' ?>><?php echo htmlspecialchars($t) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label><?php echo $T['status'] ?></label>
            <select name="estado">
                <option value=""><?php echo $T['all'] ?></option>
                <?php foreach ($estados as $e): ?>
                    <option value="<?php echo htmlspecialchars($e) ?>" <?php echo $e === $estadoFilter ? 'selected' : '' ?>><?php echo htmlspecialchars($e) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label><?php echo $T['technology'] ?></label>
            <select name="tecnologia[]" multiple size="4">
                <option value=""><?php echo $T['all'] ?></option>
                <?php foreach ($tecnologias as $t): ?>
                    <?php
                    // Comprueba si la tecnología está seleccionada en el array de filtros limpio
                    $selected = in_array($t, $tecFilter, true) ? 'selected' : '';
                    ?>
                    <option value="<?php echo htmlspecialchars($t) ?>" <?php echo $selected ?>><?php echo htmlspecialchars($t) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit"><?php echo $T['filter'] ?></button>
    </form>

    <h2><?php echo $T['projects_list'] ?></h2>

    <?php if ($totalMostrados === 0): ?>
        <p><?php echo $T['no_results'] ?></p>
    <?php else: ?>
        <p><?php echo $T['total_shown'] ?>: <strong><?php echo $totalMostrados ?></strong></p>
        <?php
        // ** USO DE LA FUNCIÓN RENOMBRADA **
        echo mostrarProyectos($resultados, $lengua, $map_tipo, $map_estado, $T);
        ?>
    <?php endif; ?>

    <div class="seleccion-accion">
        <button type="button" class="btn" onclick="window.location.href='/estructura_base_mvc/forms/proyectos/crear'">➕ Crear Nuevo Proyecto</button>
    </div>

</body>

</html>