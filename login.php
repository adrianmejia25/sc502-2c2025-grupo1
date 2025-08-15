<?php
session_start();
include("conexion.php");
$conexion->set_charset("utf8mb4");

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);
    $contrasena = $_POST['contrasena'];
    
    if (empty($correo) || empty($contrasena)) {
        $mensaje = 'Por favor, completa todos los campos.';
        $tipo_mensaje = 'error';
    } else {
        // Usar prepared statements para prevenir SQL injection
        $stmt = $conexion->prepare("SELECT id_usuario, nombre, correo, rol, `contraseña` FROM usuarios WHERE correo = ? LIMIT 1");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows == 1) {
            $usuario = $resultado->fetch_assoc();
            
            // En un sistema real, deberías usar password_verify() con contraseñas hasheadas
            // Por ahora mantenemos compatibilidad con tu sistema actual
            $hash = $usuario['contraseña'] ?? '';
            $esValida = preg_match('/^\$2y\$/', (string)$hash) ? password_verify($contrasena, $hash)
            : hash_equals((string)$hash, (string)$contrasena);

            if ($esValida) {
                $_SESSION['usuario'] = $usuario['nombre'];
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['rol'] = $usuario['rol'];
                $_SESSION['correo'] = $usuario['correo'];
                
                $mensaje = 'Bienvenido, ' . htmlspecialchars($usuario['nombre']) . '!';
                $tipo_mensaje = 'success';
                
                // Redireccionar después de 2 segundos
                header("refresh:2;url=index.php");
            } else {
                $mensaje = 'Correo o contraseña incorrectos.';
                $tipo_mensaje = 'error';
            }
        } else {
            $mensaje = 'Correo o contraseña incorrectos.';
            $tipo_mensaje = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Vinculación Académica</title>
    <link rel="stylesheet" href="estilos.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="fade-in">
            <h1><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</h1>
            <p style="text-align: center; color: white; font-size: 1.1rem; margin-bottom: 2rem;">
                Accede a tu cuenta del sistema de vinculación académica
            </p>
        </header>

        <!-- Navegación -->
        <nav class="slide-in">
            <a href="index.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="registro.php"><i class="fas fa-user-plus"></i> Registrarse</a>
        </nav>

        <!-- Formulario de Login -->
        <div class="form-container fade-in">
            <?php if (!empty($mensaje)): ?>
                <div class="message <?php echo $tipo_mensaje; ?>">
                    <i class="fas fa-<?php echo $tipo_mensaje == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <form method="post" id="loginForm">
                <div class="form-group">
                    <label for="correo">
                        <i class="fas fa-envelope"></i> Correo Electrónico
                    </label>
                    <input 
                        type="email" 
                        id="correo"
                        name="correo" 
                        placeholder="tu@correo.com" 
                        required
                        value="<?php echo isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''; ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="contrasena">
                        <i class="fas fa-lock"></i> Contraseña
                    </label>
                    <div style="position: relative;">
                        <input 
                            type="password" 
                            id="contrasena"
                            name="contrasena" 
                            placeholder="Tu contraseña" 
                            required
                        >
                        <button 
                            type="button" 
                            id="togglePassword"
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-light); cursor: pointer;"
                        >
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" name="login" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </form>

            <div style="text-align: center; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                <p style="color: var(--text-light); margin-bottom: 1rem;">
                    ¿No tienes cuenta?
                </p>
                <a href="registro.php" style="color: var(--secondary-color); text-decoration: none; font-weight: 500;">
                    <i class="fas fa-user-plus"></i> Registrarse aquí
                </a>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="grid fade-in">
            <div class="card">
                <h3><i class="fas fa-users"></i> Estudiantes</h3>
                <p>Encuentra oportunidades de práctica profesional y proyectos de vinculación que complementen tu formación académica.</p>
            </div>
            <div class="card">
                <h3><i class="fas fa-building"></i> Empresas</h3>
                <p>Conecta con talento universitario para proyectos específicos y oportunidades de colaboración académica.</p>
            </div>
            <div class="card">
                <h3><i class="fas fa-chalkboard-teacher"></i> Académicos</h3>
                <p>Supervisa y gestiona los proyectos de vinculación de tus estudiantes de manera eficiente.</p>
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
        // Animaciones de carga
        document.addEventListener('DOMContentLoaded', function() {
            const fadeElements = document.querySelectorAll('.fade-in');
            fadeElements.forEach((element, index) => {
                setTimeout(() => {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, index * 200);
            });

            // Toggle password visibility
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('contrasena');
            
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            });

            // Validación del formulario
            const form = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            
            form.addEventListener('submit', function(e) {
                const correo = document.getElementById('correo').value;
                const contrasena = document.getElementById('contrasena').value;
                
                if (!correo || !contrasena) {
                    e.preventDefault();
                    alert('Por favor, completa todos los campos.');
                    return;
                }
                
                // Mostrar loading
                loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Iniciando sesión...';
                loginBtn.disabled = true;
            });
        });
    </script>

    <style>
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease-out;
        }

        .slide-in {
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group input:invalid {
            border-color: var(--accent-color);
        }

        .form-group input:valid {
            border-color: var(--success-color);
        }
    </style>
</body>
</html>