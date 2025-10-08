<?php
// -------------------------
// DATOS: array de proyectos
// -------------------------
$proyectos = [
    [
        'nombre' => 'Intranet Corporativa',
        'descripcion' => 'Desarrollo de una intranet para centralizar documentos internos, comunicados y herramientas de gestión del personal',
        'tipo' => 'Proyecto interno',
        'estado' => 'En progreso',
        'tecnologias' => ['PHP', 'Laravel', 'MySQL', 'Bootstrap']
    ],
    [
        'nombre' => 'Portal del Cliente - Alfa',
        'descripcion' => 'Plataforma web personalizada para la gestión de incidencias y seguimiento de proyectos para un cliente externo',
        'tipo' => 'Consultoría',
        'estado' => 'En progreso',
        'tecnologias' => ['PHP', 'Symfony', 'MariaDB', 'Tailwind CSS']
    ],
    [
        'nombre' => 'Gestor de Formación Continua',
        'descripcion' => 'Aplicación para el departamento de RRHH que permite planificar, inscribir y evaluar cursos de formación interna',
        'tipo' => 'Proyecto interno',
        'estado' => 'Bloqueado',
        'tecnologias' => ['PHP', 'Laravel', 'PostgreSQL', 'Vue.js']
    ],
    [
        'nombre' => 'Evaluación del Desempeño',
        'descripcion' => 'Aplicación web para gestionar las evaluaciones anuales del personal, con informes automáticos y exportación de resultados',
        'tipo' => 'Iniciativa RRHH',
        'estado' => 'Finalizado',
        'tecnologias' => ['PHP', 'Laravel', 'MySQL', 'Chart.js']
    ],
    [
        'nombre' => 'Sistema de Control de Accesos',
        'descripcion' => 'Herramienta web para registrar y monitorizar accesos físicos y digitales de empleados, integrada con la base de datos corporativa',
        'tipo' => 'Proyecto interno',
        'estado' => 'Pendiente',
        'tecnologias' => ['PHP', 'CodeIgniter', 'MySQL', 'jQuery']
    ]
];

// -------------------------
// Calcular tipos, estados y tecnologías únicas
// -------------------------
$tipos = [];
$estados = [];
$allTechs = [];
foreach ($proyectos as $p) {
    if (!in_array($p['tipo'], $tipos)) $tipos[] = $p['tipo'];
    if (!in_array($p['estado'], $estados)) $estados[] = $p['estado'];
    foreach ($p['tecnologias'] as $t) {
        if (!in_array($t, $allTechs)) $allTechs[] = $t;
    }
}
$tecnologias = $allTechs; // nombre más claro

// -------------------------
// FUNCIONES (simples)
// -------------------------

// 1) Genera HTML (checkboxes) para la lista completa de tecnologías.
//    Recibe la lista completa ($tecnologias) y un array con las seleccionadas ($seleccionadas)
function generarCheckboxesTecnologias($tecnologias, $seleccionadas = []) {
    $html = '';
    foreach ($tecnologias as $t) {
        // generar id simple
        $id = 'tec-' . strtolower(preg_replace('/[^a-z0-9]+/i', '-', $t));
        $checked = in_array($t, $seleccionadas) ? 'checked' : '';
        $html .= '<div>';
        $html .= '<input type="checkbox" name="tecnologias[]" value="' . $t . '" id="' . $id . '" ' . $checked . '> ';
        $html .= '<label for="' . $id . '">' . $t . '</label>';
        $html .= '</div>';
    }
    return $html;
}

// 2) Recibe los datos de UN proyecto y devuelve un bloque HTML para ese proyecto.
//    Incluye un checkbox para "seleccionar" el proyecto visualmente.
function mostrarProyecto($proyecto) {
    $html = '<div class="proyecto" style="border:1px solid #ddd;padding:8px;margin:8px 0;">';
    $html .= '<input type="checkbox" name="proyecto_seleccionado[]"> ';
    $html .= '<strong>' . $proyecto['nombre'] . '</strong><br>';
    $html .= '<small><em>' . $proyecto['tipo'] . ' — ' . $proyecto['estado'] . '</em></small>';
    $html .= '<p>' . $proyecto['descripcion'] . '</p>';
    $html .= '<p><strong>Tecnologías:</strong> ' . implode(', ', $proyecto['tecnologias']) . '</p>';
    $html .= '</div>';
    return $html;
}

// -------------------------
// Recoger filtros enviados (GET)
// -------------------------
$nombreFilter = isset($_GET['nombre']) ? trim($_GET['nombre']) : '';
$tipoFilter = isset($_GET['tipo']) ? trim($_GET['tipo']) : '';
$estadoFilter = isset($_GET['estado']) ? trim($_GET['estado']) : '';
$tecsFilter = isset($_GET['tecnologias']) ? $_GET['tecnologias'] : []; // array o vacío

// -------------------------
// Filtrar proyectos (resultado)
// -------------------------
$resultados = [];
foreach ($proyectos as $p) {
    // filtro nombre (parte del nombre, no sensible a mayúsculas)
    if ($nombreFilter !== '' && stripos($p['nombre'], $nombreFilter) === false) {
        continue;
    }
    // filtro tipo (si se eligió)
    if ($tipoFilter !== '' && $p['tipo'] !== $tipoFilter) continue;
    // filtro estado
    if ($estadoFilter !== '' && $p['estado'] !== $estadoFilter) continue;
    // filtro tecnologías: si hay tecnologías seleccionadas, el proyecto debe contener todas
    $ok = true;
    if (!empty($tecsFilter)) {
        foreach ($tecsFilter as $tf) {
            if (!in_array($tf, $p['tecnologias'])) { $ok = false; break; }
        }
    }
    if (!$ok) continue;

    $resultados[] = $p;
}
$totalMostrados = count($resultados);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/estructura_base_mvc/forms/public/css/style.css">
    <title>Gestor de Proyecto</title>
    <!--<style>
        /* estilo muy básico para que se vea bien al empezar */
        body { font-family: Arial, sans-serif; margin: 18px; }
        .proyecto { background:#fff; }
        .filtros { margin-bottom: 20px; }
        .filtros div { margin:6px 0; }
    </style>-->
</head>
<body>
    <h1>Gestor de Proyectos</h1>

    <!-- Formulario de filtros (envía a la misma página usando GET) -->
    <form method="get" class="filtros">
        <div>
            <label>Nombre: <input type="text" name="nombre" value="<?= $nombreFilter ?>"></label>
        </div>

        <div>
            <label>Tipo:
                <select name="tipo">
                    <option value="">Todos</option>
                    <?php foreach ($tipos as $t): ?>
                        <option value="<?= $t ?>" <?= $t === $tipoFilter ? 'selected' : '' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>

        <div>
            <label>Estado:
                <select name="estado">
                    <option value="">Todos</option>
                    <?php foreach ($estados as $e): ?>
                        <option value="<?= $e ?>" <?= $e === $estadoFilter ? 'selected' : '' ?>><?= $e ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>

        <div>
            <label>Tecnologías:</label><br>
            <?= generarCheckboxesTecnologias($tecnologias, $tecsFilter) ?>
        </div>

        <div>
            <button type="submit">Filtrar</button>
        </div>
    </form>

    <!-- Resultados: se muestran siempre (igual que tu código original) -->
    <h2>Listado de Proyectos</h2>

    <?php if ($totalMostrados === 0): ?>
        <p>No hay proyectos que coincidan con los filtros.</p>
    <?php else: ?>
        <p>Total mostrados: <strong><?= $totalMostrados ?></strong></p>
        <?php foreach ($resultados as $p): ?>
            <?= mostrarProyecto($p) ?>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
