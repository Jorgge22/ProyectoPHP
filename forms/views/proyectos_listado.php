<?php
// Si el controlador no inyectó $proyectos, cargamos desde el modelo para evitar warnings
if (!isset($proyectos) || !is_array($proyectos)) {
    require_once __DIR__ . '/../models/Proyecto.php';
    $proyectoModel = new Proyecto();

    if (method_exists($proyectoModel, 'obtenerTodos')) {
        $proyectos = $proyectoModel->obtenerTodos();
    } else {
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
    <link rel="stylesheet" href="/estructura_base_mvc/forms/public/css/views/styleLista.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Proyectos</h1>
            <div class="page-actions">
                <!-- Botón Nuevo proyecto -->
                <button type="button" class="btn" onclick="window.location.href='/estructura_base_mvc/forms/proyectos/crear'">Nuevo proyecto</button>
                <!-- Botón Volver -->
                <button type="button" class="btn ghost" onclick="window.location.href='/estructura_base_mvc/forms/models/Model.php'">Volver</button>
            </div>
        </div>

        <div class="card table-wrap">
            <table border="1" cellpadding="6" style="margin-top:0;">
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
                    <?php if (empty($proyectos)): ?>
                        <tr>
                            <td colspan="7">No hay proyectos.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($proyectos as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['id']) ?></td>
                                <td><?= htmlspecialchars($p['nombre']) ?></td>
                                <td><?= htmlspecialchars($p['descripcion']) ?></td>
                                <td><?= htmlspecialchars($p['tipo']) ?></td>
                                <td><?= htmlspecialchars($p['estado']) ?></td>
                                <td><?= htmlspecialchars($p['tecnologias']) ?></td>
                                <td class="actions">
                                    <!-- Botón Modificar -->
                                    <button type="button" class="btn ghost" onclick="window.location.href='/estructura_base_mvc/forms/proyectos/modificar?id=<?= urlencode($p['id']) ?>'">Modificar</button>
                                    <!-- Botón Borrar con confirm -->
                                    <button type="button" class="btn ghost danger" onclick="if(confirm('¿Seguro que quieres borrar este proyecto?')) { window.location.href='/estructura_base_mvc/forms/proyectos/borrar?id=<?= urlencode($p['id']) ?>'; }">Borrar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>