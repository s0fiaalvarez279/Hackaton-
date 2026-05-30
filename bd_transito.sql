-- Crear base de datos
CREATE DATABASE IF NOT EXISTS bd_transito;
USE bd_transito;

-- Tabla usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL,
    fecha_register DATE NOT NULL
);

-- Tabla transito (vehículos/incidencias)
CREATE TABLE IF NOT EXISTS transito (
    id_transito INT AUTO_INCREMENT PRIMARY KEY,
    placa VARCHAR(20) NOT NULL,
    tipo_vehiculo VARCHAR(50) NOT NULL,
    fecha_registro DATE NOT NULL
);

-- Tabla seguimiento_transito
CREATE TABLE IF NOT EXISTS seguimiento_transito (
    id_seguimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_transito INT NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    fecha_inicio DATE NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_transito) REFERENCES transito(id_transito) ON DELETE CASCADE
);

-- Tabla reportes_transito
CREATE TABLE IF NOT EXISTS reportes_transito (
    id_reporte INT AUTO_INCREMENT PRIMARY KEY,
    id_seguimiento INT NOT NULL UNIQUE,
    puntuacion INT NOT NULL CHECK (puntuacion BETWEEN 1 AND 10),
    comentario TEXT,
    fecha_reporte DATE NOT NULL,
    FOREIGN KEY (id_seguimiento) REFERENCES seguimiento_transito(id_seguimiento) ON DELETE CASCADE
);

-- Insertar usuario de prueba (clave: admin123)
INSERT INTO usuarios (nombre, email, clave, fecha_register) VALUES
('Administrador', 'admin@transito.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', CURDATE());

-- Insertar algunos vehículos de ejemplo
INSERT INTO transito (placa, tipo_vehiculo, fecha_registro) VALUES
('ABC-123', 'Automóvil', '2024-01-15'),
('XYZ-789', 'Motocicleta', '2024-02-20'),
('LMN-456', 'Camión', '2024-03-10');

-- Insertar seguimientos de ejemplo
INSERT INTO seguimiento_transito (id_usuario, id_transito, estado, fecha_inicio) VALUES
(1, 1, 'finalizado', '2024-01-16'),
(1, 2, 'pendiente', '2024-02-21'),
(1, 3, 'en proceso', '2024-03-11');

-- Insertar reportes de ejemplo
INSERT INTO reportes_transito (id_seguimiento, puntuacion, comentario, fecha_reporte) VALUES
(1, 9, 'Conducción responsable', '2024-01-20'),
<<<<<<< HEAD
(2, 5, 'Exceso de velocidad detectado', '2024-02-25');
=======
<<<<<<< HEAD
(2, 5, 'Exceso de velocidad detectado', '2024-02-25');
=======
(2, 5, 'Exceso de velocidad detectado', '2024-02-25');
>>>>>>> de973c3c64ca69fbfc8cc06d37143c6990d9aafb
>>>>>>> 6234cab8e71b79bb5937e534df305a343dee8dad
