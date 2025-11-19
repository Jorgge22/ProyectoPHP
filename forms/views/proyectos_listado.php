<?php
// Si el controlador no inyectó $proyectos, cargamos desde el modelo para evitar warnings
if (!isset($proyectos) || !is_array($proyectos)) {
    require_once __DIR__ . '/../models/Proyecto.php';
    $proyectoModel = new Proyecto();

    if (method_exists($proyectoModel, 'obtenerTodos')) {
        $proyectos = $proyectoModel->obtenerTodos();
    } else {
        // fallback directo a DB si el método no existe
        require_once __DIR__ . '/../lib/Database.php';
        $db = (new Database())->pdo;
        $proyectos = $db->query('
            SELECT p.id, p.nombre, p.descripcion, tp.nombre AS tipo, ep.nombre AS estado,
                   GROUP_CONCAT(t.nombre SEPARATOR ", ") AS tecnologias
            FROM proyecto p
            LEFT JOIN tipoProyecto tp ON p.id_tipoProyecto = tp.id
            LEFT JOIN estadoProyecto ep ON p.id_estado = ep.id
            LEFT JOIN proyecto_tecnologia pt ON p.id = pt.id_proyecto
            LEFT JOIN tecnologia t ON pt.id_tecnologia = t.id
            GROUP BY p.id
        ')->fetchAll(PDO::FETCH_ASSOC);
    }

    if (!is_array($proyectos)) {
        $proyectos = [];
    }
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Listado de Proyectos</title>
    <link rel="stylesheet" href="/estructura_base_mvc/forms/public/css/style.css">
</head>
<body>
    <h1>Proyectos</h1>
    <a href="/estructura_base_mvc/forms/views/CrearProyecto.php">Nuevo proyecto</a>
    <table border="1" cellpadding="6" style="margin-top:16px;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Tecnologías</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($proyectos as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['id']) ?></td>
                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                    <td><?= htmlspecialchars($p['descripcion']) ?></td>
                    <td><?= htmlspecialchars($p['tipo']) ?></td>
                    <td><?= htmlspecialchars($p['estado']) ?></td>
                    <td><?= htmlspecialchars($p['tecnologias']) ?></td>
                    <td>
                        <a href="/estructura_base_mvc/forms/proyectos/modificar?id=<?= $p['id'] ?>">Modificar</a> |
                        <a href="/estructura_base_mvc/forms/proyectos/borrar?id=<?= $p['id'] ?>" onclick="return confirm('¿Seguro que quieres borrar este proyecto?')">Borrar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
