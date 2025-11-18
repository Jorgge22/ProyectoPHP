<?php
// $proyectos debe estar definido por el controlador
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
    <a href="/estructura_base_mvc/forms/proyectos/crear">Nuevo proyecto</a>
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
