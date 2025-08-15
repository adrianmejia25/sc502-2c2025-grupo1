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

-- =========================================================
-- DATOS DE PRUEBA (usuarios, ofertas, postulaciones, bitácoras)
-- =========================================================
USE vinculacion;

-- ========== 1) USUARIOS ==========
INSERT INTO usuarios (nombre, correo, `contraseña`, rol)
SELECT 'Ana Estudiante','ana@ufide.ac.cr','hash_ana','estudiante'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE correo='ana@ufide.ac.cr');

INSERT INTO usuarios (nombre, correo, `contraseña`, rol)
SELECT 'Luis Torres','luis.torres@ufide.ac.cr','hash_luis','estudiante'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE correo='luis.torres@ufide.ac.cr');

INSERT INTO usuarios (nombre, correo, `contraseña`, rol)
SELECT 'María Rojas','maria.rojas@ufide.ac.cr','hash_maria','estudiante'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE correo='maria.rojas@ufide.ac.cr');

INSERT INTO usuarios (nombre, correo, `contraseña`, rol)
SELECT 'Diego Pérez','diego.perez@ufide.ac.cr','hash_diego','estudiante'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE correo='diego.perez@ufide.ac.cr');

INSERT INTO usuarios (nombre, correo, `contraseña`, rol)
SELECT 'Sofía Jiménez','sofia.jimenez@ufide.ac.cr','hash_sofia','estudiante'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE correo='sofia.jimenez@ufide.ac.cr');

INSERT INTO usuarios (nombre, correo, `contraseña`, rol)
SELECT 'Carlos Vega','carlos.vega@ufide.ac.cr','hash_carlos','estudiante'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE correo='carlos.vega@ufide.ac.cr');

INSERT INTO usuarios (nombre, correo, `contraseña`, rol)
SELECT 'Elena Castro','elena.castro@ufide.ac.cr','hash_elena','estudiante'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE correo='elena.castro@ufide.ac.cr');

INSERT INTO usuarios (nombre, correo, `contraseña`, rol)
SELECT 'Javier Mora','javier.mora@ufide.ac.cr','hash_javier','estudiante'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE correo='javier.mora@ufide.ac.cr');

INSERT INTO usuarios (nombre, correo, `contraseña`, rol)
SELECT 'Paula Hernández','paula.hernandez@ufide.ac.cr','hash_paula','estudiante'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE correo='paula.hernandez@ufide.ac.cr');

INSERT INTO usuarios (nombre, correo, `contraseña`, rol)
SELECT 'Andrés Salas','andres.salas@ufide.ac.cr','hash_andres','estudiante'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE correo='andres.salas@ufide.ac.cr');

-- Empresas
INSERT INTO usuarios (nombre, correo, `contraseña`, rol)
SELECT 'ACME RRHH','hr@acme.com','hash_acme','empresa'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE correo='hr@acme.com');

INSERT INTO usuarios (nombre, correo, `contraseña`, rol)
SELECT 'Tecnova Talento','talento@tecnova.com','hash_tecnova','empresa'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE correo='talento@tecnova.com');

INSERT INTO usuarios (nombre, correo, `contraseña`, rol)
SELECT 'GreenSoft RRHH','rrhh@greensoft.io','hash_green','empresa'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE correo='rrhh@greensoft.io');

INSERT INTO usuarios (nombre, correo, `contraseña`, rol)
SELECT 'Soluciones XYZ','reclutamiento@xyz.co','hash_xyz','empresa'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE correo='reclutamiento@xyz.co');

INSERT INTO usuarios (nombre, correo, `contraseña`, rol)
SELECT 'DataWorks People','people@dataworks.io','hash_dw','empresa'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE correo='people@dataworks.io');

-- Académicos
INSERT INTO usuarios (nombre, correo, `contraseña`, rol)
SELECT 'Clara Académica','clara@ufide.ac.cr','hash_clara','academico'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE correo='clara@ufide.ac.cr');

INSERT INTO usuarios (nombre, correo, `contraseña`, rol)
SELECT 'Mario Docente','mario.docente@ufide.ac.cr','hash_mario','academico'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE correo='mario.docente@ufide.ac.cr');

INSERT INTO usuarios (nombre, correo, `contraseña`, rol)
SELECT 'Paula Tutor','paula.tutor@ufide.ac.cr','hash_ptutor','academico'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE correo='paula.tutor@ufide.ac.cr');

-- ========== 2) OFERTAS ==========
INSERT INTO ofertas (titulo, descripcion, modalidad, fecha_inicio, fecha_fin, id_usuario)
SELECT 'Práctica Frontend React - ACME','Soporte a UI y landing corporativa','hibrido','2025-09-01','2025-12-15', u.id_usuario
FROM usuarios u
WHERE u.correo='hr@acme.com'
  AND NOT EXISTS (SELECT 1 FROM ofertas WHERE titulo='Práctica Frontend React - ACME');

INSERT INTO ofertas (titulo, descripcion, modalidad, fecha_inicio, fecha_fin, id_usuario)
SELECT 'Desarrollo API .NET - Tecnova','Microservicios y REST con .NET','presencial','2025-09-01','2025-11-30', u.id_usuario
FROM usuarios u
WHERE u.correo='talento@tecnova.com'
  AND NOT EXISTS (SELECT 1 FROM ofertas WHERE titulo='Desarrollo API .NET - Tecnova');

INSERT INTO ofertas (titulo, descripcion, modalidad, fecha_inicio, fecha_fin, id_usuario)
SELECT 'Analista de Datos Jr - DataWorks','ETL y dashboards iniciales','remoto','2025-09-15','2025-12-31', u.id_usuario
FROM usuarios u
WHERE u.correo='people@dataworks.io'
  AND NOT EXISTS (SELECT 1 FROM ofertas WHERE titulo='Analista de Datos Jr - DataWorks');

INSERT INTO ofertas (titulo, descripcion, modalidad, fecha_inicio, fecha_fin, id_usuario)
SELECT 'App Móvil Flutter - GreenSoft','App MVP para cliente interno','hibrido','2025-09-10','2025-12-10', u.id_usuario
FROM usuarios u
WHERE u.correo='rrhh@greensoft.io'
  AND NOT EXISTS (SELECT 1 FROM ofertas WHERE titulo='App Móvil Flutter - GreenSoft');

INSERT INTO ofertas (titulo, descripcion, modalidad, fecha_inicio, fecha_fin, id_usuario)
SELECT 'Soporte TI Nivel 1 - XYZ','Mesa de ayuda y tickets','presencial','2025-09-05','2025-11-05', u.id_usuario
FROM usuarios u
WHERE u.correo='reclutamiento@xyz.co'
  AND NOT EXISTS (SELECT 1 FROM ofertas WHERE titulo='Soporte TI Nivel 1 - XYZ');

INSERT INTO ofertas (titulo, descripcion, modalidad, fecha_inicio, fecha_fin, id_usuario)
SELECT 'TCU: Plataforma de reciclaje - Municipalidad','Proyecto con comunidad local','presencial','2025-09-20','2025-11-30', u.id_usuario
FROM usuarios u
WHERE u.correo='talento@tecnova.com'
  AND NOT EXISTS (SELECT 1 FROM ofertas WHERE titulo='TCU: Plataforma de reciclaje - Municipalidad');

INSERT INTO ofertas (titulo, descripcion, modalidad, fecha_inicio, fecha_fin, id_usuario)
SELECT 'TCU: Sitio web accesible - ONG ManoAmiga','WCAG nivel AA','remoto','2025-09-20','2025-12-20', u.id_usuario
FROM usuarios u
WHERE u.correo='talento@tecnova.com'
  AND NOT EXISTS (SELECT 1 FROM ofertas WHERE titulo='TCU: Sitio web accesible - ONG ManoAmiga');

-- ========== 3) POSTULACIONES ==========

-- Ana -> Frontend (ACEPTADA)
INSERT INTO postulaciones (id_usuario, id_oferta, estado, fecha_postulacion)
SELECT u.id_usuario, o.id_oferta, 'aceptada', '2025-08-22'
FROM usuarios u JOIN ofertas o ON o.titulo='Práctica Frontend React - ACME'
WHERE u.correo='ana@ufide.ac.cr'
  AND NOT EXISTS (
    SELECT 1 FROM postulaciones p WHERE p.id_usuario=u.id_usuario AND p.id_oferta=o.id_oferta
  );

-- Ana -> Analista Datos (RECHAZADA)
INSERT INTO postulaciones (id_usuario, id_oferta, estado, fecha_postulacion)
SELECT u.id_usuario, o.id_oferta, 'rechazada', '2025-08-24'
FROM usuarios u JOIN ofertas o ON o.titulo='Analista de Datos Jr - DataWorks'
WHERE u.correo='ana@ufide.ac.cr'
  AND NOT EXISTS (SELECT 1 FROM postulaciones p WHERE p.id_usuario=u.id_usuario AND p.id_oferta=o.id_oferta);

-- Luis -> .NET (PENDIENTE)
INSERT INTO postulaciones (id_usuario, id_oferta, estado, fecha_postulacion)
SELECT u.id_usuario, o.id_oferta, 'pendiente', '2025-08-23'
FROM usuarios u JOIN ofertas o ON o.titulo='Desarrollo API .NET - Tecnova'
WHERE u.correo='luis.torres@ufide.ac.cr'
  AND NOT EXISTS (SELECT 1 FROM postulaciones p WHERE p.id_usuario=u.id_usuario AND p.id_oferta=o.id_oferta);

-- María -> .NET (ACEPTADA)
INSERT INTO postulaciones (id_usuario, id_oferta, estado, fecha_postulacion)
SELECT u.id_usuario, o.id_oferta, 'aceptada', '2025-08-23'
FROM usuarios u JOIN ofertas o ON o.titulo='Desarrollo API .NET - Tecnova'
WHERE u.correo='maria.rojas@ufide.ac.cr'
  AND NOT EXISTS (SELECT 1 FROM postulaciones p WHERE p.id_usuario=u.id_usuario AND p.id_oferta=o.id_oferta);

-- Diego -> Analista Datos (ACEPTADA)
INSERT INTO postulaciones (id_usuario, id_oferta, estado, fecha_postulacion)
SELECT u.id_usuario, o.id_oferta, 'aceptada', '2025-08-25'
FROM usuarios u JOIN ofertas o ON o.titulo='Analista de Datos Jr - DataWorks'
WHERE u.correo='diego.perez@ufide.ac.cr'
  AND NOT EXISTS (SELECT 1 FROM postulaciones p WHERE p.id_usuario=u.id_usuario AND p.id_oferta=o.id_oferta);

-- Sofía -> Flutter (ACEPTADA)
INSERT INTO postulaciones (id_usuario, id_oferta, estado, fecha_postulacion)
SELECT u.id_usuario, o.id_oferta, 'aceptada', '2025-08-26'
FROM usuarios u JOIN ofertas o ON o.titulo='App Móvil Flutter - GreenSoft'
WHERE u.correo='sofia.jimenez@ufide.ac.cr'
  AND NOT EXISTS (SELECT 1 FROM postulaciones p WHERE p.id_usuario=u.id_usuario AND p.id_oferta=o.id_oferta);

-- Carlos -> Soporte TI (ACEPTADA)
INSERT INTO postulaciones (id_usuario, id_oferta, estado, fecha_postulacion)
SELECT u.id_usuario, o.id_oferta, 'aceptada', '2025-08-22'
FROM usuarios u JOIN ofertas o ON o.titulo='Soporte TI Nivel 1 - XYZ'
WHERE u.correo='carlos.vega@ufide.ac.cr'
  AND NOT EXISTS (SELECT 1 FROM postulaciones p WHERE p.id_usuario=u.id_usuario AND p.id_oferta=o.id_oferta);

-- Elena -> Frontend (RECHAZADA)
INSERT INTO postulaciones (id_usuario, id_oferta, estado, fecha_postulacion)
SELECT u.id_usuario, o.id_oferta, 'rechazada', '2025-08-24'
FROM usuarios u JOIN ofertas o ON o.titulo='Práctica Frontend React - ACME'
WHERE u.correo='elena.castro@ufide.ac.cr'
  AND NOT EXISTS (SELECT 1 FROM postulaciones p WHERE p.id_usuario=u.id_usuario AND p.id_oferta=o.id_oferta);

-- Javier -> TCU ONG (ACEPTADA)
INSERT INTO postulaciones (id_usuario, id_oferta, estado, fecha_postulacion)
SELECT u.id_usuario, o.id_oferta, 'aceptada', '2025-08-27'
FROM usuarios u JOIN ofertas o ON o.titulo='TCU: Sitio web accesible - ONG ManoAmiga'
WHERE u.correo='javier.mora@ufide.ac.cr'
  AND NOT EXISTS (SELECT 1 FROM postulaciones p WHERE p.id_usuario=u.id_usuario AND p.id_oferta=o.id_oferta);

-- Paula H -> TCU Reciclaje (PENDIENTE)
INSERT INTO postulaciones (id_usuario, id_oferta, estado, fecha_postulacion)
SELECT u.id_usuario, o.id_oferta, 'pendiente', '2025-08-28'
FROM usuarios u JOIN ofertas o ON o.titulo='TCU: Plataforma de reciclaje - Municipalidad'
WHERE u.correo='paula.hernandez@ufide.ac.cr'
  AND NOT EXISTS (SELECT 1 FROM postulaciones p WHERE p.id_usuario=u.id_usuario AND p.id_oferta=o.id_oferta);

-- Andrés -> Analista Datos (PENDIENTE)
INSERT INTO postulaciones (id_usuario, id_oferta, estado, fecha_postulacion)
SELECT u.id_usuario, o.id_oferta, 'pendiente', '2025-08-29'
FROM usuarios u JOIN ofertas o ON o.titulo='Analista de Datos Jr - DataWorks'
WHERE u.correo='andres.salas@ufide.ac.cr'
  AND NOT EXISTS (SELECT 1 FROM postulaciones p WHERE p.id_usuario=u.id_usuario AND p.id_oferta=o.id_oferta);

-- ========== 4) BITÁCORAS ==========
-- Ana + Frontend (ACME)
INSERT INTO bitacoras (id_postulacion, observaciones, horas_realizadas, fecha)
SELECT p.id_postulacion, 'Inducción y setup', 4, '2025-09-02'
FROM postulaciones p
JOIN usuarios u ON u.id_usuario=p.id_usuario
JOIN ofertas o ON o.id_oferta=p.id_oferta
WHERE u.correo='ana@ufide.ac.cr' AND o.titulo='Práctica Frontend React - ACME' AND p.estado='aceptada'
  AND NOT EXISTS (SELECT 1 FROM bitacoras b WHERE b.id_postulacion=p.id_postulacion AND b.fecha='2025-09-02');

INSERT INTO bitacoras (id_postulacion, observaciones, horas_realizadas, fecha)
SELECT p.id_postulacion, 'Maquetación de componentes', 6, '2025-09-09'
FROM postulaciones p
JOIN usuarios u ON u.id_usuario=p.id_usuario
JOIN ofertas o ON o.id_oferta=p.id_oferta
WHERE u.correo='ana@ufide.ac.cr' AND o.titulo='Práctica Frontend React - ACME' AND p.estado='aceptada'
  AND NOT EXISTS (SELECT 1 FROM bitacoras b WHERE b.id_postulacion=p.id_postulacion AND b.fecha='2025-09-09');

-- María + .NET (Tecnova)
INSERT INTO bitacoras (id_postulacion, observaciones, horas_realizadas, fecha)
SELECT p.id_postulacion, 'Diseño de endpoints', 5, '2025-09-03'
FROM postulaciones p
JOIN usuarios u ON u.id_usuario=p.id_usuario
JOIN ofertas o ON o.id_oferta=p.id_oferta
WHERE u.correo='maria.rojas@ufide.ac.cr' AND o.titulo='Desarrollo API .NET - Tecnova' AND p.estado='aceptada'
  AND NOT EXISTS (SELECT 1 FROM bitacoras b WHERE b.id_postulacion=p.id_postulacion AND b.fecha='2025-09-03');

INSERT INTO bitacoras (id_postulacion, observaciones, horas_realizadas, fecha)
SELECT p.id_postulacion, 'Implementación y pruebas', 5, '2025-09-10'
FROM postulaciones p
JOIN usuarios u ON u.id_usuario=p.id_usuario
JOIN ofertas o ON o.id_oferta=p.id_oferta
WHERE u.correo='maria.rojas@ufide.ac.cr' AND o.titulo='Desarrollo API .NET - Tecnova' AND p.estado='aceptada'
  AND NOT EXISTS (SELECT 1 FROM bitacoras b WHERE b.id_postulacion=p.id_postulacion AND b.fecha='2025-09-10');

-- Diego + Datos (DataWorks)
INSERT INTO bitacoras (id_postulacion, observaciones, horas_realizadas, fecha)
SELECT p.id_postulacion, 'ETL inicial', 4, '2025-09-16'
FROM postulaciones p
JOIN usuarios u ON u.id_usuario=p.id_usuario
JOIN ofertas o ON o.id_oferta=p.id_oferta
WHERE u.correo='diego.perez@ufide.ac.cr' AND o.titulo='Analista de Datos Jr - DataWorks' AND p.estado='aceptada'
  AND NOT EXISTS (SELECT 1 FROM bitacoras b WHERE b.id_postulacion=p.id_postulacion AND b.fecha='2025-09-16');

INSERT INTO bitacoras (id_postulacion, observaciones, horas_realizadas, fecha)
SELECT p.id_postulacion, 'Dashboard inicial', 6, '2025-09-23'
FROM postulaciones p
JOIN usuarios u ON u.id_usuario=p.id_usuario
JOIN ofertas o ON o.id_oferta=p.id_oferta
WHERE u.correo='diego.perez@ufide.ac.cr' AND o.titulo='Analista de Datos Jr - DataWorks' AND p.estado='aceptada'
  AND NOT EXISTS (SELECT 1 FROM bitacoras b WHERE b.id_postulacion=p.id_postulacion AND b.fecha='2025-09-23');

-- Sofía + Flutter (GreenSoft)
INSERT INTO bitacoras (id_postulacion, observaciones, horas_realizadas, fecha)
SELECT p.id_postulacion, 'Pantallas base', 3, '2025-09-11'
FROM postulaciones p
JOIN usuarios u ON u.id_usuario=p.id_usuario
JOIN ofertas o ON o.id_oferta=p.id_oferta
WHERE u.correo='sofia.jimenez@ufide.ac.cr' AND o.titulo='App Móvil Flutter - GreenSoft' AND p.estado='aceptada'
  AND NOT EXISTS (SELECT 1 FROM bitacoras b WHERE b.id_postulacion=p.id_postulacion AND b.fecha='2025-09-11');

INSERT INTO bitacoras (id_postulacion, observaciones, horas_realizadas, fecha)
SELECT p.id_postulacion, 'Integración API', 5, '2025-09-18'
FROM postulaciones p
JOIN usuarios u ON u.id_usuario=p.id_usuario
JOIN ofertas o ON o.id_oferta=p.id_oferta
WHERE u.correo='sofia.jimenez@ufide.ac.cr' AND o.titulo='App Móvil Flutter - GreenSoft' AND p.estado='aceptada'
  AND NOT EXISTS (SELECT 1 FROM bitacoras b WHERE b.id_postulacion=p.id_postulacion AND b.fecha='2025-09-18');

-- Carlos + Soporte (XYZ)
INSERT INTO bitacoras (id_postulacion, observaciones, horas_realizadas, fecha)
SELECT p.id_postulacion, 'Capacitación en mesa de ayuda', 4, '2025-09-06'
FROM postulaciones p
JOIN usuarios u ON u.id_usuario=p.id_usuario
JOIN ofertas o ON o.id_oferta=p.id_oferta
WHERE u.correo='carlos.vega@ufide.ac.cr' AND o.titulo='Soporte TI Nivel 1 - XYZ' AND p.estado='aceptada'
  AND NOT EXISTS (SELECT 1 FROM bitacoras b WHERE b.id_postulacion=p.id_postulacion AND b.fecha='2025-09-06');

INSERT INTO bitacoras (id_postulacion, observaciones, horas_realizadas, fecha)
SELECT p.id_postulacion, 'Resolución de tickets', 4, '2025-09-13'
FROM postulaciones p
JOIN usuarios u ON u.id_usuario=p.id_usuario
JOIN ofertas o ON o.id_oferta=p.id_oferta
WHERE u.correo='carlos.vega@ufide.ac.cr' AND o.titulo='Soporte TI Nivel 1 - XYZ' AND p.estado='aceptada'
  AND NOT EXISTS (SELECT 1 FROM bitacoras b WHERE b.id_postulacion=p.id_postulacion AND b.fecha='2025-09-13');

-- Javier + TCU ONG (ManoAmiga)
INSERT INTO bitacoras (id_postulacion, observaciones, horas_realizadas, fecha)
SELECT p.id_postulacion, 'Revisión de pautas WCAG', 3, '2025-09-21'
FROM postulaciones p
JOIN usuarios u ON u.id_usuario=p.id_usuario
JOIN ofertas o ON o.id_oferta=p.id_oferta
WHERE u.correo='javier.mora@ufide.ac.cr' AND o.titulo='TCU: Sitio web accesible - ONG ManoAmiga' AND p.estado='aceptada'
  AND NOT EXISTS (SELECT 1 FROM bitacoras b WHERE b.id_postulacion=p.id_postulacion AND b.fecha='2025-09-21');

INSERT INTO bitacoras (id_postulacion, observaciones, horas_realizadas, fecha)
SELECT p.id_postulacion, 'Auditoría de accesibilidad', 5, '2025-09-28'
FROM postulaciones p
JOIN usuarios u ON u.id_usuario=p.id_usuario
JOIN ofertas o ON o.id_oferta=p.id_oferta
WHERE u.correo='javier.mora@ufide.ac.cr' AND o.titulo='TCU: Sitio web accesible - ONG ManoAmiga' AND p.estado='aceptada'
  AND NOT EXISTS (SELECT 1 FROM bitacoras b WHERE b.id_postulacion=p.id_postulacion AND b.fecha='2025-09-28');