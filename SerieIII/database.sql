-- ==========================================
-- Serie III - Sistema de Reservas de Equipos
-- Base de datos: reservas_equipos
-- ==========================================

DROP DATABASE IF EXISTS reservas_equipos;
CREATE DATABASE reservas_equipos CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE reservas_equipos;

CREATE TABLE equipos (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo      VARCHAR(20)  NOT NULL UNIQUE,
    nombre      VARCHAR(100) NOT NULL,
    tipo        ENUM('Laptop','Proyector','Tablet','Cámara','Laboratorio') NOT NULL,
    descripcion VARCHAR(255) NULL,
    ubicacion   VARCHAR(100) NOT NULL,
    estado      ENUM('Disponible','Mantenimiento') NOT NULL DEFAULT 'Disponible',
    creado_en   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE reservas (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    equipo_id   INT UNSIGNED NOT NULL,
    nombre      VARCHAR(100) NOT NULL,
    carne       VARCHAR(20)  NOT NULL,
    fecha       DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin    TIME NOT NULL,
    motivo      VARCHAR(255) NULL,
    creado_en   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reserva_equipo
        FOREIGN KEY (equipo_id) REFERENCES equipos(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_horas CHECK (hora_fin > hora_inicio),
    INDEX idx_equipo_fecha (equipo_id, fecha)
) ENGINE=InnoDB;

-- Datos de prueba
INSERT INTO equipos (codigo, nombre, tipo, descripcion, ubicacion, estado) VALUES
('LAP-001', 'Laptop Dell Latitude 5420',   'Laptop',      'Core i5, 16GB RAM',        'Bodega TI',          'Disponible'),
('LAP-002', 'Laptop HP ProBook 450',       'Laptop',      'Core i7, 16GB RAM',        'Bodega TI',          'Disponible'),
('LAP-003', 'MacBook Air M2',              'Laptop',      'Chip M2, 8GB RAM',         'Bodega TI',          'Mantenimiento'),
('PRO-001', 'Proyector Epson PowerLite',   'Proyector',   '3LCD, 3600 lúmenes',       'Edificio A',         'Disponible'),
('PRO-002', 'Proyector BenQ MX550',        'Proyector',   'DLP, 3600 lúmenes',        'Edificio B',         'Disponible'),
('TAB-001', 'iPad 10a generación',         'Tablet',      '64GB, Wi-Fi',              'Bodega TI',          'Disponible'),
('TAB-002', 'Samsung Galaxy Tab S9',       'Tablet',      '128GB, Wi-Fi',             'Bodega TI',          'Disponible'),
('CAM-001', 'Cámara Canon EOS R50',        'Cámara',      'Mirrorless 24MP',          'Audiovisuales',      'Disponible'),
('LAB-001', 'Laboratorio de Cómputo 1',    'Laboratorio', '30 computadoras',          'Edificio A, nivel 2','Disponible'),
('LAB-002', 'Laboratorio de Redes',        'Laboratorio', 'Equipo Cisco',             'Edificio B, nivel 1','Disponible');

INSERT INTO reservas (equipo_id, nombre, carne, fecha, hora_inicio, hora_fin, motivo) VALUES
(4, 'Ana López',    '2023-0101', '2026-11-20', '14:00', '16:00', 'Exposición del curso'),
(1, 'Carlos Pérez', '2022-0345', '2026-11-21', '08:00', '10:00', 'Práctica de programación'),
(9, 'María Ramírez','2024-0050', '2026-11-22', '10:00', '12:00', 'Clase de bases de datos');
