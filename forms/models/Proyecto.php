<?php
require_once __DIR__ . '/../lib/Database.php';

class Proyecto {
    private $db;

    public function __construct() {
        $this->db = (new Database())->pdo;
    }

    // Obtener todos los proyectos con tipo, estado y tecnologías
    public function obtenerTodos() {
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
    public function crear($nombre, $descripcion, $id_tipoProyecto, $id_estado, $tecnologias = []) {
        $sql = "INSERT INTO proyecto (nombre, descripcion, id_tipoProyecto, id_estado) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$nombre, $descripcion, $id_tipoProyecto, $id_estado]);
        $id_proyecto = $this->db->lastInsertId();
        // Insertar tecnologías asociadas
        foreach ($tecnologias as $id_tecnologia) {
            $sql2 = "INSERT INTO proyecto_tecnologia (id_proyecto, id_tecnologia) VALUES (?, ?)";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([$id_proyecto, $id_tecnologia]);
        }
        return $id_proyecto;
    }

    // Borrar un proyecto
    public function borrar($id) {
        $sql = "DELETE FROM proyecto WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // (Opcional) Modificar un proyecto
    public function modificar($id, $nombre, $descripcion, $id_tipoProyecto, $id_estado, $tecnologias = []) {
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
}
