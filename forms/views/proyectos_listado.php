<?php
// ...existing code...
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
<style>
    /* Estilo base coherente con el resto de la app */
    :root {
        --bg: #f6f7f8;
        --card: #ffffff;
        --muted: #666;
        --accent: #2b7cff;
        --danger: #e04b4b;
        --radius: 8px;
        --shadow: 0 6px 18px rgba(26, 33, 40, 0.06);
        --max-width: 1100px;
        --gap: 16px;
        --font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    * {
        box-sizing: border-box
    }

    html,
    body {
        height: 100%
    }

    body {
        margin: 0;
        font-family: var(--font-family);
        background: var(--bg);
        color: #222;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        line-height: 1.45;
    }

    /* Contenedor principal */
    .container {
        max-width: var(--max-width);
        margin: 32px auto;
        padding: 24px;
    }

    /* Tarjetas / cajas blancas */
    .card {
        background: var(--card);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 18px;
    }

    /* Cabecera de página */
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
    }

    .page-header h1 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
    }

    .page-actions a,
    .page-actions button {
        display: inline-block;
        text-decoration: none;
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        background: #fff;
        color: var(--accent);
        cursor: pointer;
        font-weight: 600;
    }

    .page-actions a:hover,
    .page-actions button:hover {
        box-shadow: 0 4px 12px rgba(43, 124, 255, 0.08)
    }

    /* Tabla estilos */
    .table-wrap {
        overflow: auto;
        background: transparent;
        margin-top: 14px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
        background: var(--card);
        border-radius: 6px;
        overflow: hidden;
    }

    thead th {
        text-align: left;
        background: linear-gradient(180deg, #fafafa, #f2f6ff);
        padding: 12px 14px;
        font-size: 13px;
        color: var(--muted);
        border-bottom: 1px solid #e9eef6;
    }

    tbody td {
        padding: 12px 14px;
        vertical-align: top;
        font-size: 14px;
        border-bottom: 1px solid #f1f4f8;
    }

    tbody tr:last-child td {
        border-bottom: 0
    }

    /* Acciones */
    .actions a {
        color: var(--accent);
        text-decoration: none;
        margin-right: 8px;
        font-size: 13px;
    }

    .actions a.danger {
        color: var(--danger)
    }

    /* Formularios */
    .form-row {
        display: flex;
        gap: var(--gap);
        flex-wrap: wrap;
    }

    .form-group {
        flex: 1 1 260px;
        display: flex;
        flex-direction: column;
        margin-bottom: 12px;
    }

    label {
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 6px;
        font-weight: 600;
    }

    input[type="text"],
    input[type="search"],
    textarea,
    select {
        border: 1px solid #e3e8ef;
        background: #fff;
        padding: 10px 12px;
        border-radius: 6px;
        font-size: 14px;
        outline: none;
    }

    textarea {
        min-height: 110px;
        resize: vertical;
    }

    select[multiple] {
        height: auto;
        min-height: 90px;
    }

    /* Botones principales */
    .btn {
        display: inline-block;
        padding: 10px 14px;
        border-radius: 6px;
        background: var(--accent);
        color: #fff;
        text-decoration: none;
        border: 0;
        cursor: pointer;
        font-weight: 700;
    }

    .btn.ghost {
        background: transparent;
        color: var(--accent);
        border: 1px solid rgba(43, 124, 255, 0.12);
    }

    /* Mensajes */
    .note {
        font-size: 13px;
        color: var(--muted);
    }

    /* Responsive */
    @media (max-width:900px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px
        }

        table {
            min-width: 700px
        }

        .form-row {
            flex-direction: column
        }
    }

    /* pequeños ajustes visuales para confirm dialog links */
    a[onclick] {
        text-decoration: underline;
    }

    /* Mantener compatibilidad con clases ya usadas en vistas */
    .login {
        max-width: 520px;
        margin: 0 auto;
        padding: 18px;
    }

    .error {
        color: var(--danger);
        margin-top: 8px;
        font-weight: 600;
    }
</style>

<body>
    <div class="container">
        <div class="page-header">
            <h1>Proyectos</h1>
            <div class="page-actions">
                <!-- Botón Nuevo proyecto -->
                <button type="button" class="btn" onclick="window.location.href='/estructura_base_mvc/forms/proyectos/crear'">Nuevo proyecto</button>
                <!-- Botón Volver -->
                <button type="button" class="btn ghost" onclick="window.location.href='/estructura_base_mvc/forms/'">Volver</button>
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