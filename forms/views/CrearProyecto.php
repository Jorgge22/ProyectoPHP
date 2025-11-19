<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/estructura_base_mvc/forms/public/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creación de Proyecto</title>
</head>

<body>
    <h2>➕ Crear Nuevo Proyecto</h2>

    <form action="/estructura_base_mvc/forms/proyectos/crear" method="post">

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required> <br><br>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" id="descripcion"></textarea><br><br>

        <label for="id_tipoProyecto">Tipo:</label>
        <select name="id_tipoProyecto" id="id_tipoProyecto" required>
            <?php foreach ($tipos as $tipo): ?>
                <option value="<?php echo htmlspecialchars($tipo['id']) ?>">
                    <?php echo htmlspecialchars($tipo['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label for="id_estado">Estado:</label>
        <select name="id_estado" id="id_estado" required>
            <?php foreach ($estados as $estado): ?>
                <option value="<?php echo htmlspecialchars($estado['id']) ?>">
                    <?php echo htmlspecialchars($estado['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Tecnologías (selecciona una o varias):</label><br>
        <?php foreach ($tecnologias as $tec): ?>
            <input
                type="checkbox"
                name="tecnologias[]"
                value="<?php echo htmlspecialchars($tec['id']) ?>"
                id="tec-<?php echo htmlspecialchars($tec['id']) ?>">
            <label for="tec-<?php echo htmlspecialchars($tec['id']) ?>">
                <?php echo htmlspecialchars($tec['nombre']) ?>
            </label><br>
        <?php endforeach; ?>
        <br>

        <input type="submit" value="Crear Proyecto">
    </form>
</body>

</html>