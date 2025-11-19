<?php
// Variables esperadas: $tipos, $estados, $tecnologias, $proyecto (opcional para modificar)
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title><?= isset($proyecto) ? 'Modificar' : 'Nuevo' ?> Proyecto</title>
    <link rel="stylesheet" href="/estructura_base_mvc/forms/public/css/style.css">
</head>

<body>
    <h1><?= isset($proyecto) ? 'Modificar' : 'Nuevo' ?> Proyecto</h1>
    <form method="post" action="<?= isset($proyecto) ? '/estructura_base_mvc/forms/proyectos/modificar' : '/estructura_base_mvc/forms/proyectos/crear' ?>">
        <?php if (isset($proyecto)): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($proyecto['id']) ?>">
        <?php endif; ?>
        <label>Nombre:</label>
        <input type="text" name="nombre" required value="<?= isset($proyecto) ? htmlspecialchars($proyecto['nombre']) : '' ?>">
        <br>
        <label>Descripción:</label>
        <textarea name="descripcion" required><?= isset($proyecto) ? htmlspecialchars($proyecto['descripcion']) : '' ?></textarea>
        <br>
        <label>Tipo de proyecto:</label>
        <label for="id_tipoProyecto">Tipo:</label>
        <select name="id_tipoProyecto" id="id_tipoProyecto" required>
            <?php foreach ($tipos as $tipo): ?>
                <option value="<?php echo $tipo['id'] ?>">
                    <?php echo htmlspecialchars($tipo['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label for="id_estado">Estado:</label>
        <select name="id_estado" id="id_estado" required>
            <?php foreach ($estados as $estado): ?>
                <option value="<?php echo $estado['id'] ?>">
                    <?php echo htmlspecialchars($estado['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Tecnologías:</label><br>
        <?php foreach ($tecnologias as $tec): ?>
            <input type="checkbox" name="tecnologias[]" value="<?php echo $tec['id'] ?>" id="tec-<?php echo $tec['id'] ?>">
            <label for="tec-<?php echo $tec['id'] ?>"><?php echo htmlspecialchars($tec['nombre']) ?></label><br>
        <?php endforeach; ?>
        </select>
        <br>
        <button type="submit">Guardar</button>
        <a href="/estructura_base_mvc/forms/proyectos">Cancelar</a>
    </form>
</body>

</html>