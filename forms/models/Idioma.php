<?php
// devuelve un array con las claves de texto en el idioma pedido
function traducir($idioma = 'es') {
    $en = [
        'title' => 'Project Manager',
        'login_title' => 'Login',
        'user' => 'User',
        'password' => 'Password',
        'login' => 'Login',
        'logout' => 'Logout',
        'invalid_credentials' => 'Invalid username or password.',
        'name_or_desc' => 'Name or description:',
        'type' => 'Type',
        'status' => 'Status',
        'technology' => 'Technology',
        'filter' => 'Filter',
        'all' => 'All',
        'total_shown' => 'Total shown',
        'no_results' => 'No projects match the filters.',
        'language' => 'Language',
        'projects_list' => 'Projects list'
    ];

    $es = [
        'title' => 'Gestor de Proyectos',
        'login_title' => 'Acceso',
        'user' => 'Usuario',
        'password' => 'Contraseña',
        'login' => 'Entrar',
        'logout' => 'Cerrar sesión',
        'invalid_credentials' => 'Usuario o contraseña incorrectos.',
        'name_or_desc' => 'Nombre o descripción:',
        'type' => 'Tipo',
        'status' => 'Estado',
        'technology' => 'Tecnología',
        'filter' => 'Filtrar',
        'all' => 'Todos',
        'total_shown' => 'Total mostrados',
        'no_results' => 'No hay proyectos que coincidan con los filtros.',
        'language' => 'Idioma',
        'projects_list' => 'Listado de Proyectos'
    ];

    return ($idioma === 'en') ? $en : $es;
}
