<?php
// index.php (Temporalmente)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'lib/Router.php';

// Crear instancia del Router
$router = new Router();

// Definir rutas simples

// Definir rutas simples (incluye variantes con y sin slash)

$router->add('/estructura_base_mvc/forms/index.php', 'InicioController@index');
$router->add('/estructura_base_mvc/forms/', 'InicioController@index');
$router->add('/estructura_base_mvc/forms', 'InicioController@index');
$router->add('/estructura_base_mvc/forms/procesarDatos', 'InicioController@procesarDatos');

// Rutas para gestión de proyectos
$router->add('/estructura_base_mvc/forms/proyectos', 'ProyectoController@index');
$router->add('/estructura_base_mvc/forms/proyectos/crear', 'ProyectoController@crearForm');
$router->add('/estructura_base_mvc/forms/proyectos/crear', 'ProyectoController@crear'); // POST
$router->add('/estructura_base_mvc/forms/proyectos/borrar', 'ProyectoController@borrar');
$router->add('/estructura_base_mvc/forms/proyectos/modificar', 'ProyectoController@modificarForm');
$router->add('/estructura_base_mvc/forms/proyectos/modificar', 'ProyectoController@modificar'); // POST

// Procesar la ruta actual
$router->dispatch($_SERVER['REQUEST_URI']);
