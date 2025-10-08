<?php
require 'lib/Router.php';

// Crear instancia del Router
$router = new Router();

// Definir rutas simples
$router->add('/estructura_base_mvc/forms/index.php', 'InicioController@index');
$router->add('/estructura_base_mvc/forms/procesarDatos', 'InicioController@procesarDatos');
// Procesar la ruta actual

$router->dispatch($_SERVER['REQUEST_URI']);

?>