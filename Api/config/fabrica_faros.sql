-- CREACIÓN DE LA BASE DE DATOS

CREATE DATABASE IF NOT EXISTS fabrica_faros
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE fabrica_faros;


-- TABLA CLIENTES (MARCAS)

CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);


-- TABLA MODELOS

CREATE TABLE modelos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    UNIQUE (cliente_id, nombre)
);


-- TABLA PIEZAS

CREATE TABLE piezas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    denominacion VARCHAR(255) NOT NULL,
    modelo_id INT NOT NULL,
    FOREIGN KEY (modelo_id) REFERENCES modelos(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


-- TABLA PROGRAMAS DE NECESIDADES

CREATE TABLE programas_necesidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    fecha_solicitud DATE NOT NULL,
    fecha_entrega DATE,
    estado ENUM('pendiente','aprobado','rechazado') DEFAULT 'pendiente',
    observaciones TEXT,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


-- TABLA PEDIDOS

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    programa_id INT NOT NULL,
    fecha_pedido DATE DEFAULT (CURRENT_DATE),
    estado ENUM('pendiente','produccion','enviado','entregado') DEFAULT 'pendiente',
    FOREIGN KEY (programa_id) REFERENCES programas_necesidades(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


-- DETALLE DE PEDIDOS

CREATE TABLE detalle_pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    pieza_id INT NOT NULL,
    cantidad INT NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (pieza_id) REFERENCES piezas(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


-- INSERTS DE CLIENTES

INSERT INTO clientes (nombre) VALUES
('Audi'),
('BMW'),
('Volkswagen'),
('Seat'),
('Renault'),
('Ford'),
('Opel'),
('Bugatti');


-- INSERTS DE MODELOS

-- Audi
INSERT INTO modelos (cliente_id, nombre)
SELECT id, 'A1' FROM clientes WHERE nombre = 'Audi';

INSERT INTO modelos (cliente_id, nombre)
SELECT id, 'Q8 RL' FROM clientes WHERE nombre = 'Audi';

-- BMW
INSERT INTO modelos (cliente_id, nombre)
SELECT id, 'F22' FROM clientes WHERE nombre = 'BMW';

INSERT INTO modelos (cliente_id, nombre)
SELECT id, 'G20' FROM clientes WHERE nombre = 'BMW';

-- Volkswagen
INSERT INTO modelos (cliente_id, nombre)
SELECT id, 'Golf A8' FROM clientes WHERE nombre = 'Volkswagen';

INSERT INTO modelos (cliente_id, nombre)
SELECT id, 'Polo' FROM clientes WHERE nombre = 'Volkswagen';

-- Seat
INSERT INTO modelos (cliente_id, nombre)
SELECT id, 'Leon' FROM clientes WHERE nombre = 'Seat';


-- INSERTS DE PIEZAS

INSERT INTO piezas (codigo, denominacion, modelo_id)
SELECT '90112502','SOPORTE INF DRL AUDI AU270 LED I', m.id
FROM modelos m
JOIN clientes c ON m.cliente_id = c.id
WHERE c.nombre = 'Audi' AND m.nombre = 'A1';

INSERT INTO piezas (codigo, denominacion, modelo_id)
SELECT '90112503','SOPORTE SUP DRL AUDI AU270 LED D', m.id
FROM modelos m
JOIN clientes c ON m.cliente_id = c.id
WHERE c.nombre = 'Audi' AND m.nombre = 'A1';

INSERT INTO piezas (codigo, denominacion, modelo_id)
SELECT '90137358','LB REFLECTOR INY BMW F22 LCI EST/DIN TI', m.id
FROM modelos m
JOIN clientes c ON m.cliente_id = c.id
WHERE c.nombre = 'BMW' AND m.nombre = 'F22';

INSERT INTO piezas (codigo, denominacion, modelo_id)
SELECT '90137360','LB REFLECTOR INY BMW G20 LCI DIN TI', m.id
FROM modelos m
JOIN clientes c ON m.cliente_id = c.id
WHERE c.nombre = 'BMW' AND m.nombre = 'G20';

INSERT INTO piezas (codigo, denominacion, modelo_id)
SELECT '90221451','SOPORTE DRL VW GOLF A8 IZQ', m.id
FROM modelos m
JOIN clientes c ON m.cliente_id = c.id
WHERE c.nombre = 'Volkswagen' AND m.nombre = 'Golf A8';

INSERT INTO piezas (codigo, denominacion, modelo_id)
SELECT '90221452','SOPORTE DRL VW GOLF A8 DER', m.id
FROM modelos m
JOIN clientes c ON m.cliente_id = c.id
WHERE c.nombre = 'Volkswagen' AND m.nombre = 'Golf A8';

INSERT INTO piezas (codigo, denominacion, modelo_id)
SELECT '90311510','SOPORTE DRL SEAT LEON IZQ', m.id
FROM modelos m
JOIN clientes c ON m.cliente_id = c.id
WHERE c.nombre = 'Seat' AND m.nombre = 'Leon';

INSERT INTO piezas (codigo, denominacion, modelo_id)
SELECT '90311511','SOPORTE DRL SEAT LEON DER', m.id
FROM modelos m
JOIN clientes c ON m.cliente_id = c.id
WHERE c.nombre = 'Seat' AND m.nombre = 'Leon';