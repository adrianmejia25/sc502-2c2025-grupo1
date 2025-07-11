<?php include("conexion.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Oferta - Sistema de Vinculación Académica</title>
    <link rel="stylesheet" href="estilos.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="fade-in">
            <h1><i class="fas fa-graduation-cap"></i> Sistema de Vinculación Académica</h1>
            <p style="text-align: center; color: white; font-size: 1.2rem; margin-bottom: 2rem;">
                Universidad Fidélitas - Publicar Nueva Oferta
            </p>
        </header>

        <!-- Navegación -->
        <nav class="slide-in">
            <a href="index.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="registro.php"><i class="fas fa-user-plus"></i> Registro</a>
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
            <a href="postular.php"><i class="fas fa-paper-plane"></i> Postular</a>
            <a href="bitacora.php"><i class="fas fa-book"></i> Bitácora</a>
        </nav>

        <!-- Formulario de Publicar Oferta -->
        <div class="form-container fade-in">
            <h2><i class="fas fa-briefcase"></i> Publicar Oferta</h2>
            
            <?php
            if (isset($_POST['publicar'])) {
                // Validación básica
                $titulo = trim($_POST['titulo']);
                $descripcion = trim($_POST['descripcion']);
                $modalidad = $_POST['modalidad'];
                $fecha_inicio = $_POST['fecha_inicio'];
                $fecha_fin = $_POST['fecha_fin'];
                $id_usuario = $_POST['id_usuario'];
                
                if (empty($titulo) || empty($fecha_inicio) || empty($fecha_fin) || empty($id_usuario)) {
                    echo '<div class="message error"><i class="fas fa-exclamation-triangle"></i> Por favor, complete todos los campos obligatorios.</div>';
                } elseif (strtotime($fecha_inicio) >= strtotime($fecha_fin)) {
                    echo '<div class="message error"><i class="fas fa-exclamation-triangle"></i> La fecha de fin debe ser posterior a la fecha de inicio.</div>';
                } elseif (strtotime($fecha_inicio) < strtotime(date('Y-m-d'))) {
                    echo '<div class="message error"><i class="fas fa-exclamation-triangle"></i> La fecha de inicio no puede ser anterior a hoy.</div>';
                } else {
                    // Verificar que el usuario existe
                    $check_user = $conexion->prepare("SELECT id FROM usuarios WHERE id = ?");
                    $check_user->bind_param("i", $id_usuario);
                    $check_user->execute();
                    $result = $check_user->get_result();
                    
                    if ($result->num_rows == 0) {
                        echo '<div class="message error"><i class="fas fa-exclamation-triangle"></i> El ID de usuario no existe.</div>';
                    } else {
                        $stmt = $conexion->prepare("INSERT INTO ofertas(titulo, descripcion, modalidad, fecha_inicio, fecha_fin, id_usuario) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("sssssi", $titulo, $descripcion, $modalidad, $fecha_inicio, $fecha_fin, $id_usuario);
                        
                        if ($stmt->execute()) {
                            echo '<div class="message success"><i class="fas fa-check-circle"></i> Oferta publicada correctamente. <a href="index.php">Ver ofertas</a></div>';
                        } else {
                            echo '<div class="message error"><i class="fas fa-exclamation-triangle"></i> Error al publicar la oferta.</div>';
                        }
                    }
                }
            }
            ?>
            
            <form method="post" class="fade-in">
                <div class="form-group">
                    <label for="titulo"><i class="fas fa-tag"></i> Título de la Oferta</label>
                    <input type="text" name="titulo" id="titulo" placeholder="Ej: Práctica Profesional en Desarrollo Web" required>
                </div>
                
                <div class="form-group">
                    <label for="descripcion"><i class="fas fa-align-left"></i> Descripción</label>
                    <textarea name="descripcion" id="descripcion" placeholder="Describe los requisitos, responsabilidades y beneficios de la oferta..." rows="5"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="modalidad"><i class="fas fa-map-marker-alt"></i> Modalidad</label>
                    <select name="modalidad" id="modalidad" required>
                        <option value="">Seleccione la modalidad</option>
                        <option value="presencial">
                            <i class="fas fa-building"></i> Presencial
                        </option>
                        <option value="remoto">
                            <i class="fas fa-home"></i> Remoto
                        </option>
                        <option value="hibrido">
                            <i class="fas fa-laptop-house"></i> Híbrido
                        </option>
                    </select>
                </div>
                
                <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="fecha_inicio"><i class="fas fa-calendar-alt"></i> Fecha de Inicio</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="fecha_fin"><i class="fas fa-calendar-check"></i> Fecha de Fin</label>
                        <input type="date" name="fecha_fin" id="fecha_fin" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="id_usuario"><i class="fas fa-user"></i> ID Usuario Empresa</label>
                    <input type="number" name="id_usuario" id="id_usuario" placeholder="Ingrese su ID de usuario" required min="1">
                </div>
                
                <button type="submit" name="publicar" class="btn-primary">
                    <i class="fas fa-upload"></i> Publicar Oferta
                </button>
            </form>
        </div>

        <!-- Información sobre modalidades -->
        <div class="grid fade-in">
            <div class="card">
                <h3><i class="fas fa-building"></i> Presencial</h3>
                <p>La práctica o trabajo se realiza completamente en las instalaciones de la empresa u organización.</p>
            </div>
            <div class="card">
                <h3><i class="fas fa-home"></i> Remoto</h3>
                <p>La práctica o trabajo se realiza completamente desde casa o cualquier ubicación remota.</p>
            </div>
            <div class="card">
                <h3><i class="fas fa-laptop-house"></i> Híbrido</h3>
                <p>Combina trabajo presencial y remoto, ofreciendo flexibilidad en la modalidad de trabajo.</p>
            </div>
        </div>

        <!-- Tips para publicar ofertas -->
        <div class="card fade-in">
            <h3><i class="fas fa-lightbulb"></i> Tips para una Oferta Exitosa</h3>
            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
                <div>
                    <h4><i class="fas fa-check"></i> Título Claro</h4>
                    <p>Use un título descriptivo que resuma la posición y área de trabajo.</p>
                </div>
                <div>
                    <h4><i class="fas fa-list"></i> Descripción Detallada</h4>
                    <p>Incluya requisitos, responsabilidades y beneficios específicos.</p>
                </div>
                <div>
                    <h4><i class="fas fa-calendar"></i> Fechas Realistas</h4>
                    <p>Establezca fechas que permitan una planificación adecuada.</p>
                </div>
                <div>
                    <h4><i class="fas fa-handshake"></i> Expectativas Claras</h4>
                    <p>Defina claramente qué espera del candidato y qué puede ofrecer.</p>
                </div>
            </div>
        </div>

<!-- Footer -->
        <footer style="text-align: center; margin-top: 3rem; padding: 2rem; background: var(--white); border-radius: var(--border-radius); box-shadow: var(--shadow);">
            <p style="color: var(--text-light);">
                <i class="fas fa-university"></i> Universidad Fidélitas - Sistema de Vinculación Académica
            </p>
            <p style="color: var(--text-light); font-size: 0.9rem;">
                © 2025 Todos los derechos reservados | Desarrollado con <i class="fas fa-heart" style="color: var(--accent-color);"></i>
            </p>
        </footer>
    </div>

    <script>
        // Animaciones al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const fadeElements = document.querySelectorAll('.fade-in');
            fadeElements.forEach((element, index) => {
                setTimeout(() => {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });

        // Validación de fechas
        document.getElementById('fecha_inicio').addEventListener('change', function() {
            const fechaInicio = this.value;
            const fechaFin = document.getElementById('fecha_fin');
            
            if (fechaInicio) {
                fechaFin.min = fechaInicio;
                
                // Si la fecha de fin es menor que la de inicio, resetearla
                if (fechaFin.value && fechaFin.value <= fechaInicio) {
                    fechaFin.value = '';
                }
            }
        });

        // Validación del formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            const fechaInicio = document.getElementById('fecha_inicio').value;
            const fechaFin = document.getElementById('fecha_fin').value;
            
            if (fechaInicio && fechaFin && fechaInicio >= fechaFin) {
                e.preventDefault();
                alert('La fecha de fin debe ser posterior a la fecha de inicio');
            }
        });
    </script>

    <style>
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease-out;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--secondary-color), var(--primary-color));
            color: var(--white);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            width: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        h4 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }
    </style>
</body>
</html>