<?php

class InicioController {
    public function index() {
        if ( array_key_exists("msg",$_GET)){
            $message = $_GET["msg"];
        }else
        {
            $message = "¡Hola desde el HomeController!";
        }
        
        require_once 'views/inicio.php';
    }


    public function procesarDatos()
    {
        var_dump($_GET);
    }
}
