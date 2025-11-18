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
        <select name="id_tipoProyecto" required>
            <?php foreach ($tipos as $t): ?>
                <option value="<?= $t['id'] ?>" <?= isset($proyecto) && $proyecto['id_tipoProyecto'] == $t['id'] ? 'selected' : '' ?>><?= htmlspecialchars($t['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <label>Estado:</label>
        <select name="id_estado" required>
            <?php foreach ($estados as $e): ?>
                <option value="<?= $e['id'] ?>" <?= isset($proyecto) && $proyecto['id_estado'] == $e['id'] ? 'selected' : '' ?>><?= htmlspecialchars($e['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <label>Tecnologías:</label>
        <select name="tecnologias[]" multiple size="5">
            <?php foreach ($tecnologias as $tec): ?>
                <option value="<?= $tec['id'] ?>"
                    <?php if (isset($proyecto) && isset($proyecto['tecnologias_ids']) && in_array($tec['id'], $proyecto['tecnologias_ids'])) echo 'selected'; ?>>
                    <?= htmlspecialchars($tec['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>
        <button type="submit">Guardar</button>
        <a href="/estructura_base_mvc/forms/proyectos">Cancelar</a>
    </form>
</body>
</html>
