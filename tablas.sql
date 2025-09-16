CREATE DATABASE viviendas;
USE viviendas;

CREATE TABLE usuarios (
  documento VARCHAR(20) PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  apellido1 VARCHAR(100),
  apellido2 VARCHAR(100),
  contrasena VARCHAR(255) NOT NULL,
  correo VARCHAR(100) NOT NULL UNIQUE,
  motivo_ingreso TEXT
);

CREATE TABLE usuario_coop (
  documento VARCHAR(20) PRIMARY KEY,
  FOREIGN KEY (documento) REFERENCES usuarios(documento)
);

CREATE TABLE administrativo (
  id_admin INT AUTO_INCREMENT PRIMARY KEY,
  documento VARCHAR(20) NOT NULL,
  FOREIGN KEY (documento) REFERENCES usuarios(documento)
);

CREATE TABLE unidades_habitacionales (
  num_puerta INT PRIMARY KEY,
  direccion VARCHAR(255)
);

CREATE TABLE registro_autenticacion (
  id_registro INT AUTO_INCREMENT PRIMARY KEY,
  documento VARCHAR(20) NOT NULL,
  estado ENUM('pendiente', 'aceptado', 'rechazado') NOT NULL,
  FOREIGN KEY (documento) REFERENCES usuarios(documento)
);

CREATE TABLE horas_trabajadas (
  id_hora INT AUTO_INCREMENT PRIMARY KEY,
  documento VARCHAR(20) NOT NULL,
  horas DECIMAL(5,2),
  fecha DATE,
  motivo TEXT,
  FOREIGN KEY (documento) REFERENCES usuarios(documento)
);

CREATE TABLE comprobante_pago (
  id_comprobante INT AUTO_INCREMENT PRIMARY KEY,
  documento VARCHAR(20) NOT NULL,
  archivo_pdf VARCHAR(255),
  estado ENUM('pendiente', 'aprobado', 'rechazado') NOT NULL,
  tipo ENUM('inicial','mensual','compensatorio') NOT NULL DEFAULT 'mensual',
  fecha_subida DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (documento) REFERENCES usuarios(documento)
);

CREATE TABLE asigna (
  num_puerta INT NOT NULL,
  documento VARCHAR(20) NOT NULL,
  PRIMARY KEY (num_puerta, documento),
  FOREIGN KEY (num_puerta) REFERENCES unidades_habitacionales(num_puerta),
  FOREIGN KEY (documento) REFERENCES usuarios(documento)
);

CREATE TABLE presenta (
  id_comprobante INT NOT NULL,
  documento VARCHAR(20) NOT NULL,
  PRIMARY KEY (id_comprobante, documento),
  FOREIGN KEY (id_comprobante) REFERENCES comprobante_pago(id_comprobante),
  FOREIGN KEY (documento) REFERENCES usuarios(documento)
);

CREATE TABLE aprueba (
  id_admin INT NOT NULL,
  id_comprobante INT NOT NULL,
  documento VARCHAR(20) NOT NULL,
  PRIMARY KEY (id_admin, id_comprobante, documento),
  FOREIGN KEY (id_admin) REFERENCES administrativo(id_admin),
  FOREIGN KEY (id_comprobante) REFERENCES comprobante_pago(id_comprobante),
  FOREIGN KEY (documento) REFERENCES usuarios(documento)
);

CREATE TABLE acepta (
  id_admin INT NOT NULL,
  id_registro INT NOT NULL,
  documento VARCHAR(20) NOT NULL,
  PRIMARY KEY (id_admin, id_registro, documento),
  FOREIGN KEY (id_admin) REFERENCES administrativo(id_admin),
  FOREIGN KEY (id_registro) REFERENCES registro_autenticacion(id_registro),
  FOREIGN KEY (documento) REFERENCES usuarios(documento)
);

CREATE TABLE registra_horas (
  id_hora INT NOT NULL,
  documento VARCHAR(20) NOT NULL,
  PRIMARY KEY (id_hora, documento),
  FOREIGN KEY (id_hora) REFERENCES horas_trabajadas(id_hora),
  FOREIGN KEY (documento) REFERENCES usuarios(documento)
);
