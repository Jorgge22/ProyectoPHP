<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Inicio</title>
    <!-- <link rel="stylesheet" href="/estructura_base_mvc/forms/public/css/style.css" /> -->
    <script src="/estructura_base_mvc/forms/public/js/main.js"></script>
</head>

<body>
    <header>
        <h1>Gestor de Proyectos</h1>
        <button>+ Nuevo Proyecto</button>
    </header>

    <main>
        <h3>Filtrar Proyectos</h3>
        <form action="/estructura_base_mvc/forms/models/Model.php" method="get">
            <div class="campo">
                <label for="nombre">Nombre del Proyecto</label>
                <input type="text" id="nombre" name="nombre" placeholder="Buscar por nombre...">
            </div>

            <div class="campo">
                <label for="tipo">Tipo de Proyecto</label>
                <select id="tipo" name="tipo">
                    <option value="">Todos los tipos</option>
                    <option value="interno">Proyecto interno</option>
                    <option value="consultoria">Consultoría</option>
                    <option value="rrhh">Iniciativa RRHH</option>
                </select>
            </div>

            <div class="campo">
                <label>Tecnologías</label>
                <div>
                    <input type="checkbox" name="tecnologias[]" value="react" id="react">
                    <label for="react">React</label>
                </div>
                <div>
                    <input type="checkbox" name="tecnologias[]" value="vue" id="vue">
                    <label for="vue">Vue.js</label>
                </div>
                <div>
                    <input type="checkbox" name="tecnologias[]" value="angular" id="angular">
                    <label for="angular">Angular</label>
                </div>
                <div>
                    <input type="checkbox" name="tecnologias[]" value="node" id="node">
                    <label for="node">Node.js</label>
                </div>
                <div>
                    <input type="checkbox" name="tecnologias[]" value="python" id="python">
                    <label for="python">Python</label>
                </div>
                <div>
                    <input type="checkbox" name="tecnologias[]" value="php" id="php">
                    <label for="php">PHP</label>
                </div>
                <div>
                    <input type="checkbox" name="tecnologias[]" value="laravel" id="laravel">
                    <label for="laravel">Laravel</label>
                </div>
                <div>
                    <input type="checkbox" name="tecnologias[]" value="mysql" id="mysql">
                    <label for="mysql">MySQL</label>
                </div>
                <div>
                    <input type="checkbox" name="tecnologias[]" value="bootstrap" id="bootstrap">
                    <label for="bootstrap">Bootstrap</label>
                </div>
                <div>
                    <input type="checkbox" name="tecnologias[]" value="symfony" id="symfony">
                    <label for="symfony">Symfony</label>
                </div>
                <div>
                    <input type="checkbox" name="tecnologias[]" value="mariadb" id="mariadb">
                    <label for="mariadb">MariaDB</label>
                </div>
                <div>
                    <input type="checkbox" name="tecnologias[]" value="tailwind-css" id="tailwind-css">
                    <label for="tailwind-css">Tailwind CSS</label>
                </div>
                <div>
                    <input type="checkbox" name="tecnologias[]" value="postgresql" id="postgresql">
                    <label for="postgresql">Postgre SQL</label>
                </div>
                <div>
                    <input type="checkbox" name="tecnologias[]" value="chartjs" id="chartjs">
                    <label for="chartjs">Chart.js</label>
                </div>
                <div>
                    <input type="checkbox" name="tecnologias[]" value="codeigniter" id="codeigniter">
                    <label for="codeigniter">CodeIgniter</label>
                </div>
                <div>
                    <input type="checkbox" name="tecnologias[]" value="jquery" id="jquery">
                    <label for="jquery">jQuery</label>
                </div>
            </div>

            <div class="campo">
                <label for="estado">Estado</label>
                <select id="estado" name="estado">
                    <option value="">Todos los estados</option>
                    <option value="progreso">En progreso</option>
                    <option value="bloqueado">Bloqueado</option>
                    <option value="finalizado">Finalizado</option>
                    <option value="pendiente">Pendiente</option>
                </select>
            </div>

            <div class="acciones">
                <div>
                    <input type="submit" value="Aplicar filtros" ="Model.php">
                    <a href="#">↻ Limpiar filtros</a>
                </div>
            </div>
        </form>
    </main>
</body>

</html>