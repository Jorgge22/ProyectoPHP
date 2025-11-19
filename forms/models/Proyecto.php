<?php
require_once __DIR__ . '/../lib/Database.php';

class Proyecto
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->pdo;
    }

    /**
     * Obtiene todos los tipos de proyecto
     * @return array
     */
    public function obtenerTipos()
    {
        $sql = "SELECT id, nombre FROM tipoProyecto ORDER BY nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todos los estados de proyecto
     * @return array
     */
    public function obtenerEstados()
    {
        $sql = "SELECT id, nombre FROM estadoProyecto ORDER BY nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todas las tecnologías
     * @return array
     */
    public function obtenerTecnologias()
    {
        $sql = "SELECT id, nombre FROM tecnologia ORDER BY nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todos los proyectos con tipo, estado y tecnologías
    public function obtenerTodos()
    {
        $sql = "SELECT p.id, p.nombre, p.descripcion, tp.nombre AS tipo, ep.nombre AS estado,
                       GROUP_CONCAT(t.nombre) AS tecnologias
                FROM proyecto p
                JOIN tipoProyecto tp ON p.id_tipoProyecto = tp.id
                JOIN estadoProyecto ep ON p.id_estado = ep.id
                LEFT JOIN proyecto_tecnologia pt ON p.id = pt.id_proyecto
                LEFT JOIN tecnologia t ON pt.id_tecnologia = t.id
                GROUP BY p.id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    // Crear un nuevo proyecto
    public function crear($nombre, $descripcion, $id_tipoProyecto, $id_estado, $tecnologias = [])
    {
        // 1. Inserción del proyecto principal
        $sql = "INSERT INTO proyecto (nombre, descripcion, id_tipoProyecto, id_estado) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$nombre, $descripcion, $id_tipoProyecto, $id_estado]);
        $id_proyecto = $this->db->lastInsertId(); // Obtiene el ID del proyecto recién creado

        // 2. Inserción de tecnologías asociadas (si las hay)
        foreach ($tecnologias as $id_tecnologia) {
            $sql2 = "INSERT INTO proyecto_tecnologia (id_proyecto, id_tecnologia) VALUES (?, ?)";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([$id_proyecto, $id_tecnologia]);
        }

        return $id_proyecto; // Devuelve el ID del nuevo proyecto
    }

    // Borrar un proyecto
    public function borrar($id)
    {
        $sql = "DELETE FROM proyecto WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Modificar un proyecto
    public function modificar($id, $nombre, $descripcion, $id_tipoProyecto, $id_estado, $tecnologias = [])
    {
        $sql = "UPDATE proyecto SET nombre = ?, descripcion = ?, id_tipoProyecto = ?, id_estado = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$nombre, $descripcion, $id_tipoProyecto, $id_estado, $id]);
        // Actualizar tecnologías asociadas
        $sqlDel = "DELETE FROM proyecto_tecnologia WHERE id_proyecto = ?";
        $stmtDel = $this->db->prepare($sqlDel);
        $stmtDel->execute([$id]);
        foreach ($tecnologias as $id_tecnologia) {
            $sql2 = "INSERT INTO proyecto_tecnologia (id_proyecto, id_tecnologia) VALUES (?, ?)";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([$id, $id_tecnologia]);
        }
        return true;
    }


    public function obtenerPorId($id)
    {
        require_once __DIR__ . '/../lib/Database.php';
        $db = (new Database())->pdo;

        $stmt = $db->prepare('
            SELECT p.id, p.nombre, p.descripcion, p.id_tipoProyecto, p.id_estado,
                   tp.nombre AS tipo, ep.nombre AS estado
            FROM proyecto p
            LEFT JOIN tipoProyecto tp ON p.id_tipoProyecto = tp.id
            LEFT JOIN estadoProyecto ep ON p.id_estado = ep.id
            WHERE p.id = ?
        ');
        $stmt->execute([$id]);
        $proyecto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($proyecto) {
            $stmt2 = $db->prepare('
                SELECT t.id, t.nombre
                FROM tecnologia t
                JOIN proyecto_tecnologia pt ON t.id = pt.id_tecnologia
                WHERE pt.id_proyecto = ?
            ');
            $stmt2->execute([$id]);
            $proyecto['tecnologias'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }

        return $proyecto ?: null;
    }

    // obtener ids de tecnologías asociadas a un proyecto
    public function obtenerTecnologiasPorProyecto($id)
    {
        require_once __DIR__ . '/../lib/Database.php';
        $db = (new Database())->pdo;
        $stmt = $db->prepare('SELECT id_tecnologia FROM proyecto_tecnologia WHERE id_proyecto = ?');
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
