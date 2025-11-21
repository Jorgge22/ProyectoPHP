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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /estructura_base_mvc/forms/proyectos');
            exit;
        }

        // obtener datos del formulario
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $id_tipoProyecto = (int)($_POST['id_tipoProyecto'] ?? 0);
        $id_estado = (int)($_POST['id_estado'] ?? 0);
        $tecnologias = $_POST['tecnologias'] ?? []; // array de ids

        try {
            // si el modelo proporciona crear(), úsalo
            if (method_exists($this->proyectoModel, 'crear')) {
                $this->proyectoModel->crear($nombre, $descripcion, $id_tipoProyecto, $id_estado, $tecnologias);
            } else {
                // fallback directo a BD
                require_once __DIR__ . '/../lib/Database.php';
                $db = (new Database())->pdo;

                $stmt = $db->prepare('INSERT INTO proyecto (nombre, descripcion, id_tipoProyecto, id_estado) VALUES (?, ?, ?, ?)');
                $stmt->execute([$nombre, $descripcion, $id_tipoProyecto, $id_estado]);
                $idProyecto = $db->lastInsertId();

                if (!empty($tecnologias) && is_array($tecnologias)) {
                    $stmtRel = $db->prepare('INSERT INTO proyecto_tecnologia (id_proyecto, id_tecnologia) VALUES (?, ?)');
                    foreach ($tecnologias as $idTec) {
                        $stmtRel->execute([$idProyecto, (int)$idTec]);
                    }
                }
            }

            // redirigir al listado tras crear
            header('Location: /estructura_base_mvc/forms/proyectos');
            exit;
        } catch (\Throwable $e) {
            error_log('ProyectoController::crear error: ' . $e->getMessage());
            echo 'Error al crear proyecto.';
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
    // controllers/ProyectoController.php

    public function modificarForm()
    {
        // 1. Recoger ID de la URL
        $id = $_GET['id'] ?? null;

        // 2. Cargar listas de apoyo
        $tipos = $this->proyectoModel->obtenerTipos();
        $estados = $this->proyectoModel->obtenerEstados();
        $tecnologias = $this->proyectoModel->obtenerTecnologias();

        // 3. BUSCAR EL PROYECTO Y GUARDARLO EN LA VARIABLE $proyecto
        $proyecto = null;
        if ($id) {
            $proyecto = $this->proyectoModel->obtenerPorId($id);
        }

        // 4. Cargar la vista (que ahora recibirá $proyecto con datos)
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
