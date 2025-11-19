<?php
require_once __DIR__ . '/../models/Proyecto.php';

class ProyectoController
{
    private $proyectoModel;

    public function __construct()
    {
        $this->proyectoModel = new Proyecto();
    }

    // Listar todos los proyectos
    public function index()
    {
        // 1. Obtener proyectos (usando el método con filtros que sugerimos antes)
        $proyectos = $this->proyectoModel->obtenerTodos();
        // 2. Obtener datos de soporte usando el Modelo (¡Corregido!)
        $tipos = $this->proyectoModel->obtenerTipos();
        $estados = $this->proyectoModel->obtenerEstados();
        $tecnologias = $this->proyectoModel->obtenerTecnologias();

        // 3. Cargar Vista
        require __DIR__ . '/../views/proyectos_listado.php';
    }

    // Mostrar formulario de creación
    public function crearForm()
    {
        // Obtener datos de soporte usando el Modelo (¡Corregido!)
        $tipos = $this->proyectoModel->obtenerTipos();
        $estados = $this->proyectoModel->obtenerEstados();
        $tecnologias = $this->proyectoModel->obtenerTecnologias();

        // Cargar la vista. Le pasamos los datos para que el usuario pueda seleccionar.
        require __DIR__ . '/../views/CrearProyecto.php'; 
    }

    // Crear proyecto 
    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $id_tipoProyecto = $_POST['id_tipoProyecto'] ?? 1;
            $id_estado = $_POST['id_estado'] ?? 1;
            $tecnologias = $_POST['tecnologias'] ?? [];
            $this->proyectoModel->crear($nombre, $descripcion, $id_tipoProyecto, $id_estado, $tecnologias);
            header('Location: /estructura_base_mvc/forms/proyectos');
            exit;
        }
    }

    // Borrar proyecto
    public function borrar()
    {
        if (isset($_GET['id'])) {
            $this->proyectoModel->borrar($_GET['id']);
            header('Location: /estructura_base_mvc/forms/proyectos');
            exit;
        }
    }

    // Método Modificar 
    public function modificarForm()
    {
        $id = $_GET['id'] ?? null;
        $proyecto = null;
        $tecnologias_ids = [];

        // 1. Obtener datos de soporte usando el Modelo
        $tipos = $this->proyectoModel->obtenerTipos();
        $estados = $this->proyectoModel->obtenerEstados();
        $tecnologias = $this->proyectoModel->obtenerTecnologias();

        // 2. Obtener el proyecto específico 
        if ($id) {
            require_once __DIR__ . '/../lib/Database.php';
            $db = (new Database())->pdo;
            $stmt = $db->prepare('SELECT * FROM proyecto WHERE id = ?');
            $stmt->execute([$id]);
            $proyecto = $stmt->fetch();
        }

        require __DIR__ . '/../views/CrearProyecto.php';
    }
    public function modificar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $id_tipoProyecto = $_POST['id_tipoProyecto'] ?? 1;
            $id_estado = $_POST['id_estado'] ?? 1;
            $tecnologias = $_POST['tecnologias'] ?? [];
            $this->proyectoModel->modificar($id, $nombre, $descripcion, $id_tipoProyecto, $id_estado, $tecnologias);
            header('Location: /estructura_base_mvc/forms/proyectos');
            exit;
        }
    }
}
