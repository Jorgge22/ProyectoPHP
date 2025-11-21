<?php
// views/CrearProyecto.php

// Detectamos si estamos editando (si $proyecto tiene datos)
$is_editing = isset($proyecto) && $proyecto;

// URL destino: Si editamos -> actualizar. Si creamos -> crear.
$action_url = $is_editing
    ? '/estructura_base_mvc/forms/proyectos/actualizar'
    : '/estructura_base_mvc/forms/proyectos/guardar';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/estructura_base_mvc/forms/public/css/style.css">
    <title><?php echo $is_editing ? 'Modificar' : 'Crear'; ?> Proyecto</title>
</head>
<style>
    /* --- 1. Estilos Generales --- */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 40px;
        background: #f6f7f8;
        color: #333;
    }

    h2 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 30px;
        font-size: 28px;
    }

    /* --- 2. Contenedor del Formulario (Tarjeta Centrada) --- */
    form {
        background: #fff;
        padding: 40px;
        border-radius: 10px;
        margin: 0 auto;
        max-width: 700px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        border: 1px solid #eaeaea;
    }

    /* --- 3. Etiquetas (Labels) --- */
    label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        margin-top: 20px;
        color: #444;
    }

    label:first-child {
        margin-top: 0;
    }

    /* --- 4. Campos de Texto y Selectores --- */
    input[type="text"],
    select,
    textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        box-sizing: border-box;
        font-size: 15px;
        background-color: #fff;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    input[type="text"]:focus,
    select:focus,
    textarea:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        outline: none;
    }

    textarea {
        resize: vertical;
        min-height: 120px;
        font-family: inherit;
    }

    /* --- 5. Sección de Tecnologías (FLEXBOX - EN LÍNEA) --- */
    .tecnologias-container {
        /* Esto hace que se pongan una al lado de la otra */
        display: flex;
        flex-wrap: wrap;
        /* Permite que bajen de línea si no caben */
        gap: 10px;
        /* Espacio entre etiquetas */

        background: #f9fafb;
        padding: 20px;
        border: 1px solid #eee;
        border-radius: 8px;
        margin-top: 8px;
    }

    /* Estilo para cada etiqueta de tecnología */
    .tecnologia-item {
        display: flex;
        align-items: center;
        justify-content: center;

        /* Esto hace que las cajas se estiren para ocupar espacio */
        flex-grow: 1;
        flex-basis: auto;

        font-weight: normal;
        margin: 0;
        padding: 10px 15px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 20px;
        /* Bordes redondeados */
        cursor: pointer;
        transition: all 0.2s;
        user-select: none;
    }

    .tecnologia-item:hover {
        background: #f0f7ff;
        border-color: #007bff;
        transform: translateY(-1px);
    }

    .tecnologia-item input[type="checkbox"] {
        margin-right: 8px;
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: #007bff;
    }

    /* --- 6. Botón de Enviar --- */
    input[type="submit"] {
        display: block;
        width: 100%;
        margin-top: 35px;
        padding: 14px;
        border: none;
        border-radius: 6px;
        background: linear-gradient(to right, #007bff, #0056b3);
        color: white;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: transform 0.1s, box-shadow 0.2s;
        box-shadow: 0 4px 6px rgba(0, 123, 255, 0.2);
    }

    input[type="submit"]:hover {
        background: linear-gradient(to right, #0069d9, #004494);
        transform: translateY(-1px);
        box-shadow: 0 6px 8px rgba(0, 123, 255, 0.3);
    }
</style>

<body>

    <h2><?php echo $is_editing ? '✏️ Modificar Proyecto' : '➕ Crear Nuevo Proyecto'; ?></h2>

    <form action="<?php echo $action_url; ?>" method="post">

        <?php if ($is_editing): ?>
            <input type="hidden" name="id" value="<?php echo $proyecto['id']; ?>">
        <?php endif; ?>

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre"
            value="<?php echo $is_editing ? htmlspecialchars($proyecto['nombre']) : ''; ?>" required>
        <br><br>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion"
            id="descripcion"><?php echo $is_editing ? htmlspecialchars($proyecto['descripcion']) : ''; ?></textarea>
        <br><br>

        <label for="id_tipoProyecto">Tipo:</label>
        <select name="id_tipoProyecto" id="id_tipoProyecto" required>
            <?php foreach ($tipos as $tipo): ?>
                <option value="<?php echo $tipo['id']; ?>" <?php echo ($is_editing && $proyecto['id_tipoProyecto'] == $tipo['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($tipo['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label for="id_estado">Estado:</label>
        <select name="id_estado" id="id_estado" required>
            <?php foreach ($estados as $estado): ?>
                <option value="<?php echo $estado['id']; ?>" <?php echo ($is_editing && $proyecto['id_estado'] == $estado['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($estado['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Tecnologías:</label><br>
        <?php
        // Sacamos las IDs de tecnologías de este proyecto (si existen)
        $ids_tec_proyecto = $is_editing ? ($proyecto['tecnologias_ids'] ?? []) : [];
        ?>
        <?php foreach ($tecnologias as $tec): ?>
            <input type="checkbox" name="tecnologias[]" value="<?php echo $tec['id']; ?>" id="tec-<?php echo $tec['id']; ?>"
                <?php echo in_array($tec['id'], $ids_tec_proyecto) ? 'checked' : ''; ?>>
            <label for="tec-<?php echo $tec['id']; ?>">
                <?php echo htmlspecialchars($tec['nombre']); ?>
            </label><br>
        <?php endforeach; ?>
        <br>

        <input type="submit" value="<?php echo $is_editing ? 'Guardar Cambios' : 'Crear Proyecto'; ?>">
    </form>

</body>

</html>