
CREATE DATABASE viviendas;
USE viviendas;


CREATE TABLE usuarios (
  documento VARCHAR(20) PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  contrasena VARCHAR(255) NOT NULL,
  correo VARCHAR(100) NOT NULL UNIQUE,
  motivo_ingreso TEXT
);


CREATE TABLE administrativo (
  id_admin INT AUTO_INCREMENT PRIMARY KEY,
  documento VARCHAR(20) NOT NULL,
  FOREIGN KEY (documento) REFERENCES usuarios(documento) ON DELETE CASCADE,
  INDEX idx_documento (documento)
);


CREATE TABLE unidades_habitacionales (
  num_puerta INT PRIMARY KEY,
  direccion VARCHAR(255),
  INDEX idx_direccion (direccion)
);


CREATE TABLE registro_autenticacion (
  id_registro INT AUTO_INCREMENT PRIMARY KEY,
  documento VARCHAR(20) NOT NULL,
  estado ENUM('pendiente', 'aceptado', 'rechazado') NOT NULL DEFAULT 'pendiente',
  fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (documento) REFERENCES usuarios(documento) ON DELETE CASCADE,
  INDEX idx_estado (estado),
  INDEX idx_documento (documento)
);


CREATE TABLE horas_trabajadas (
  id_hora INT AUTO_INCREMENT PRIMARY KEY,
  documento VARCHAR(20) NOT NULL,
  horas DECIMAL(5,2) NOT NULL,
  fecha DATE NOT NULL,
  motivo TEXT,
  FOREIGN KEY (documento) REFERENCES usuarios(documento) ON DELETE CASCADE,
  INDEX idx_documento (documento),
  INDEX idx_fecha (fecha)
);


CREATE TABLE comprobante_pago (
  id_comprobante INT AUTO_INCREMENT PRIMARY KEY,
  documento VARCHAR(20) NOT NULL,
  archivo_pdf VARCHAR(255),
  estado ENUM('pendiente', 'aprobado', 'rechazado') NOT NULL DEFAULT 'pendiente',
  tipo ENUM('inicial', 'mensual', 'compensatorio') NOT NULL DEFAULT 'mensual',
  fecha_subida DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (documento) REFERENCES usuarios(documento) ON DELETE CASCADE,
  INDEX idx_documento (documento),
  INDEX idx_estado (estado),
  INDEX idx_tipo (tipo),
  INDEX idx_fecha (fecha_subida)
);


CREATE TABLE asigna (
  num_puerta INT NOT NULL,
  documento VARCHAR(20) NOT NULL,
  fecha_asignacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (num_puerta, documento),
  FOREIGN KEY (num_puerta) REFERENCES unidades_habitacionales(num_puerta) ON DELETE CASCADE,
  FOREIGN KEY (documento) REFERENCES usuarios(documento) ON DELETE CASCADE,
  INDEX idx_documento (documento)
);

INSERT INTO unidades_habitacionales (num_puerta, direccion) VALUES
(101, 'Calle Principal 101'),
(102, 'Calle Principal 102'),
(103, 'Calle Principal 103'),
(104, 'Calle Principal 104'),
(105, 'Calle Principal 105'),
(201, 'Calle Secundaria 201'),
(202, 'Calle Secundaria 202'),
(203, 'Calle Secundaria 203'),
(204, 'Calle Secundaria 204'),
(205, 'Calle Secundaria 205');

INSERT INTO usuarios (documento, nombre, correo, contrasena, motivo_ingreso) 
VALUES ('admin', 'Administrador', 'admin@nuevoamanecer.coop', 'admin123', 'Usuario administrador del sistema')
ON DUPLICATE KEY UPDATE nombre=nombre;

INSERT INTO administrativo (documento) 
VALUES ('admin')
ON DUPLICATE KEY UPDATE documento=documento;