# sc502-2c2025-grupo1
# Prototipo hecho por el grupo

## Script de Base de Datos
```sql
CREATE DATABASE IF NOT EXISTS vinculacion;
USE vinculacion;

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    correo VARCHAR(100) UNIQUE,
    contraseña VARCHAR(100),
    rol ENUM('estudiante', 'empresa', 'academico')
);

CREATE TABLE ofertas (
    id_oferta INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100),
    descripcion TEXT,
    modalidad ENUM('presencial', 'remoto', 'hibrido'),
    fecha_inicio DATE,
    fecha_fin DATE,
    id_usuario INT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

CREATE TABLE postulaciones (
    id_postulacion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_oferta INT,
    estado ENUM('pendiente', 'aceptada', 'rechazada'),
    fecha_postulacion DATE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_oferta) REFERENCES ofertas(id_oferta)
);

CREATE TABLE bitacoras (
    id_bitacora INT AUTO_INCREMENT PRIMARY KEY,
    id_postulacion INT,
    observaciones TEXT,
    horas_realizadas INT,
    fecha DATE,
    FOREIGN KEY (id_postulacion) REFERENCES postulaciones(id_postulacion)
);
