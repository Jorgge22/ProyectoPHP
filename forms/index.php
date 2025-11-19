<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/lib/Router.php';

// Crear instancia del Router
$router = new Router();

// Rutas de inicio
$router->add('/estructura_base_mvc/forms', 'InicioController@index');
$router->add('/estructura_base_mvc/forms/', 'InicioController@index');
$router->add('/estructura_base_mvc/forms/index.php', 'InicioController@index');
$router->add('/estructura_base_mvc/forms/procesarDatos', 'InicioController@procesarDatos');

// Rutas de proyectos (MVC)
$router->add('/estructura_base_mvc/forms/proyectos', 'ProyectoController@index');
$router->add('/estructura_base_mvc/forms/proyectos/', 'ProyectoController@index');

// Formulario creación / guardado
$router->add('/estructura_base_mvc/forms/proyectos/crear', 'ProyectoController@crearForm');
$router->add('/estructura_base_mvc/forms/proyectos/guardar', 'ProyectoController@crear');

// Modificar / actualizar
$router->add('/estructura_base_mvc/forms/proyectos/modificar', 'ProyectoController@modificarForm');
$router->add('/estructura_base_mvc/forms/proyectos/actualizar', 'ProyectoController@modificar');

// Borrar
$router->add('/estructura_base_mvc/forms/proyectos/borrar', 'ProyectoController@borrar');

// Despachar la ruta actual
$router->dispatch($_SERVER['REQUEST_URI']);