CREATE DATABASE estructura_mvc DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE estructura_mvc;

CREATE TABLE tipoProyecto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE estadoProyecto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE tecnologia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE proyecto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255),
    id_tipoProyecto INT NOT NULL,
    id_estado INT NOT NULL,
    FOREIGN KEY (id_tipoProyecto) REFERENCES tipoProyecto(id),
    FOREIGN KEY (id_estado) REFERENCES estadoProyecto(id)
);

CREATE TABLE proyecto_tecnologia (
    id_proyecto INT NOT NULL,
    id_tecnologia INT NOT NULL,
    PRIMARY KEY (id_proyecto, id_tecnologia),
    FOREIGN KEY (id_proyecto) REFERENCES proyecto(id) ON DELETE CASCADE,
    FOREIGN KEY (id_tecnologia) REFERENCES tecnologia(id) ON DELETE CASCADE
);

-- Tipos de proyecto
INSERT INTO tipoProyecto (nombre) VALUES
('Proyecto interno'),
('Consultoria'),
('Iniciativa RRHH');

-- Estados de proyecto
INSERT INTO estadoProyecto (nombre) VALUES
('En progreso'),
('Bloqueado'),
('Finalizado'),
('Pendiente');

-- Tecnologías
INSERT INTO tecnologia (nombre) VALUES
('PHP'),
('Laravel'),
('MySQL'),
('Bootstrap'),
('Symfony'),
('MariaDB'),
('Tailwind CSS'),
('PostgreSQL'),
('Vue.js'),
('Chart.js'),
('CodeIgniter'),
('jQuery');

-- Proyectos
INSERT INTO proyecto (nombre, descripcion, id_tipoProyecto, id_estado) VALUES
('Intranet Corporativa', 'Desarrollo de una intranet para centralizar documentos internos, comunicados y herramientas de gestion del personal', 1, 1),
('Portal del Cliente - Alfa', 'Plataforma web personalizada para la gestion de incidencias y seguimiento de proyectos para un cliente externo', 2, 1),
('Gestor de Formacion Continua', 'Aplicacion para el departamento de rrhh que permite planificar, inscribir y evaluar cursos de formacion interna', 1, 2),
('Evaluacion del Desempeno', 'Aplicacion web para gestionar las evaluaciones anuales del personal, con informes automaticos y exportacion de resultados', 3, 3),
('Sistema de Control de Accesos', 'Herramienta web para registrar y monitorizar accesos fisicos y digitales de empleados, integrada con la base de datos corporativa', 1, 4);

-- Relación proyecto_tecnologia
-- Intranet Corporativa (id=1)
INSERT INTO proyecto_tecnologia (id_proyecto, id_tecnologia) VALUES (1, 1), (1, 2), (1, 3), (1, 4);
-- Portal del Cliente - Alfa (id=2)
INSERT INTO proyecto_tecnologia (id_proyecto, id_tecnologia) VALUES (2, 1), (2, 5), (2, 6), (2, 7);
-- Gestor de Formacion Continua (id=3)
INSERT INTO proyecto_tecnologia (id_proyecto, id_tecnologia) VALUES (3, 1), (3, 2), (3, 8), (3, 9);
-- Evaluacion del Desempeno (id=4)
INSERT INTO proyecto_tecnologia (id_proyecto, id_tecnologia) VALUES (4, 1), (4, 2), (4, 3), (4, 10);
-- Sistema de Control de Accesos (id=5)
INSERT INTO proyecto_tecnologia (id_proyecto, id_tecnologia) VALUES (5, 1), (5, 11), (5, 3), (5, 12);