<?php
require_once __DIR__ . '/../models/Proyecto.php';

class ProyectoController {
    private $proyectoModel;

    public function __construct() {
        $this->proyectoModel = new Proyecto();
    }

    // Listar todos los proyectos
    public function index() {
        $proyectos = $this->proyectoModel->obtenerTodos();
        require_once __DIR__ . '/../lib/Database.php';
        $db = (new Database())->pdo;
        $tipos = $db->query('SELECT * FROM tipoProyecto')->fetchAll();
        $estados = $db->query('SELECT * FROM estadoProyecto')->fetchAll();
        $tecnologias = $db->query('SELECT * FROM tecnologia')->fetchAll();
        require __DIR__ . '/../views/proyectos_listado.php';
    }

    // Mostrar formulario de creación
    public function crearForm() {
        require_once __DIR__ . '/../lib/Database.php';
        $db = (new Database())->pdo;
        $tipos = $db->query('SELECT * FROM tipoProyecto')->fetchAll();
        $estados = $db->query('SELECT * FROM estadoProyecto')->fetchAll();
        $tecnologias = $db->query('SELECT * FROM tecnologia')->fetchAll();
        require __DIR__ . '/../views/proyecto_form.php';
    }

    // Crear proyecto (POST)
    public function crear() {
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
    public function borrar() {
        if (isset($_GET['id'])) {
            $this->proyectoModel->borrar($_GET['id']);
            header('Location: /estructura_base_mvc/forms/proyectos');
            exit;
        }
    }

    // (Opcional) Modificar proyecto
    public function modificarForm() {
        require_once __DIR__ . '/../lib/Database.php';
        $db = (new Database())->pdo;
        $tipos = $db->query('SELECT * FROM tipoProyecto')->fetchAll();
        $estados = $db->query('SELECT * FROM estadoProyecto')->fetchAll();
        $tecnologias = $db->query('SELECT * FROM tecnologia')->fetchAll();
        $id = $_GET['id'] ?? null;
        $proyecto = null;
        $tecnologias_ids = [];
        if ($id) {
            $stmt = $db->prepare('SELECT * FROM proyecto WHERE id = ?');
            $stmt->execute([$id]);
            $proyecto = $stmt->fetch();
            $proyecto['tecnologias_ids'] = [];
            $stmtTec = $db->prepare('SELECT id_tecnologia FROM proyecto_tecnologia WHERE id_proyecto = ?');
            $stmtTec->execute([$id]);
            foreach ($stmtTec->fetchAll() as $row) {
                $proyecto['tecnologias_ids'][] = $row['id_tecnologia'];
            }
        }
        require __DIR__ . '/../views/proyecto_form.php';
    }
    public function modificar() {
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
