<?php include("conexion.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - Sistema de Vinculación Académica</title>
    <link rel="stylesheet" href="estilos.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="fade-in">
            <h1><i class="fas fa-graduation-cap"></i> Sistema de Vinculación Académica</h1>
            <p style="text-align: center; color: white; font-size: 1.2rem; margin-bottom: 2rem;">
                Universidad Fidélitas - Registro de Nuevo Usuario
            </p>
        </header>

        <!-- Navegación -->
        <nav class="slide-in">
            <a href="index.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
            <a href="publicar_oferta.php"><i class="fas fa-briefcase"></i> Publicar Oferta</a>
            <a href="postular.php"><i class="fas fa-paper-plane"></i> Postular</a>
            <a href="bitacora.php"><i class="fas fa-book"></i> Bitácora</a>
        </nav>

        <!-- Formulario de Registro -->
        <div class="form-container fade-in">
            <h2><i class="fas fa-user-plus"></i> Registro de Usuario</h2>
            
            <?php
            if (isset($_POST['registrar'])) {
                // Validación básica
                $nombre = trim($_POST['nombre']);
                $correo = trim($_POST['correo']);
                $contrasena = $_POST['contrasena'];
                $rol = $_POST['rol'];
                
                if (empty($nombre) || empty($correo) || empty($contrasena)) {
                    echo '<div class="message error"><i class="fas fa-exclamation-triangle"></i> Por favor, complete todos los campos obligatorios.</div>';
                } else {
                    // Verificar si el correo ya existe
                    $check_email = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
                    $check_email->bind_param("s", $correo);
                    $check_email->execute();
                    $result = $check_email->get_result();
                    
                    if ($result->num_rows > 0) {
                        echo '<div class="message error"><i class="fas fa-exclamation-triangle"></i> El correo electrónico ya está registrado.</div>';
                    } else {
                        // Hash de la contraseña
                        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
                        
                        $stmt = $conexion->prepare("INSERT INTO usuarios(nombre, correo, contraseña, rol) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("ssss", $nombre, $correo, $contrasena_hash, $rol);
                        
                        if ($stmt->execute()) {
                            echo '<div class="message success"><i class="fas fa-check-circle"></i> Usuario registrado correctamente. <a href="login.php">Iniciar sesión</a></div>';
                        } else {
                            echo '<div class="message error"><i class="fas fa-exclamation-triangle"></i> Error al registrar el usuario.</div>';
                        }
                    }
                }
            }
            ?>
            
            <form method="post" class="fade-in">
                <div class="form-group">
                    <label for="nombre"><i class="fas fa-user"></i> Nombre Completo</label>
                    <input type="text" name="nombre" id="nombre" placeholder="Ingrese su nombre completo" required>
                </div>
                
                <div class="form-group">
                    <label for="correo"><i class="fas fa-envelope"></i> Correo Electrónico</label>
                    <input type="email" name="correo" id="correo" placeholder="correo@ejemplo.com" required>
                </div>
                
                <div class="form-group">
                    <label for="contrasena"><i class="fas fa-lock"></i> Contraseña</label>
                    <input type="password" name="contrasena" id="contrasena" placeholder="Ingrese una contraseña segura" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="rol"><i class="fas fa-user-tag"></i> Tipo de Usuario</label>
                    <select name="rol" id="rol" required>
                        <option value="">Seleccione su rol</option>
                        <option value="estudiante">
                            <i class="fas fa-graduation-cap"></i> Estudiante
                        </option>
                        <option value="empresa">
                            <i class="fas fa-building"></i> Empresa
                        </option>
                        <option value="academico">
                            <i class="fas fa-chalkboard-teacher"></i> Académico
                        </option>
                    </select>
                </div>
                
                <button type="submit" name="registrar" class="btn-primary">
                    <i class="fas fa-user-plus"></i> Registrarse
                </button>
            </form>
            
            <div class="text-center mt-2">
                <p>¿Ya tienes cuenta? <a href="login.php" style="color: var(--secondary-color);">Iniciar sesión</a></p>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="grid fade-in">
            <div class="card">
                <h3><i class="fas fa-graduation-cap"></i> Para Estudiantes</h3>
                <p>Accede a oportunidades de prácticas profesionales, proyectos de vinculación y empleos que complementen tu formación académica.</p>
            </div>
            <div class="card">
                <h3><i class="fas fa-building"></i> Para Empresas</h3>
                <p>Conecta con talento universitario para proyectos, prácticas y oportunidades laborales que impulsen tu organización.</p>
            </div>
            <div class="card">
                <h3><i class="fas fa-chalkboard-teacher"></i> Para Académicos</h3>
                <p>Supervisa y gestiona los procesos de vinculación de tus estudiantes con el sector productivo.</p>
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

        // Validación del formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('contrasena').value;
            if (password.length < 6) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 6 caracteres');
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
    </style>
</body>
</html>