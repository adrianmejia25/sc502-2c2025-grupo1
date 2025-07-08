<?php
session_start();
include("conexion.php");

// Obtener estadísticas del sistema
$total_usuarios = $conexion->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
$total_ofertas = $conexion->query("SELECT COUNT(*) as total FROM ofertas")->fetch_assoc()['total'];
$total_postulaciones = $conexion->query("SELECT COUNT(*) as total FROM postulaciones")->fetch_assoc()['total'];
$ofertas_activas = $conexion->query("SELECT COUNT(*) as total FROM ofertas WHERE fecha_fin >= CURDATE()")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Vinculación Académica - Universidad Fidélitas</title>
    <link rel="stylesheet" href="estilos.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="fade-in">
            <h1><i class="fas fa-graduation-cap"></i> Sistema de Vinculación Académica</h1>
            <p style="text-align: center; color: white; font-size: 1.2rem; margin-bottom: 2rem;">
                Universidad Fidélitas - Conectando talento con oportunidades
            </p>
        </header>

        <!-- Navegación -->
        <nav class="slide-in">
            <?php if (isset($_SESSION['usuario'])): ?>
                <span style="color: var(--success-color); font-weight: bold;">
                    <i class="fas fa-user"></i> Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?>
                </span>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            <?php else: ?>
                <a href="registro.php"><i class="fas fa-user-plus"></i> Registro</a>
                <a href="login.php"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
            <?php endif; ?>
            
            <a href="publicar_oferta.php"><i class="fas fa-briefcase"></i> Publicar Oferta</a>
            <a href="postular.php"><i class="fas fa-paper-plane"></i> Postular</a>
            <a href="bitacora.php"><i class="fas fa-book"></i> Bitácora</a>
        </nav>

        <!-- Estadísticas -->
        <section class="grid fade-in">
            <div class="card text-center">
                <i class="fas fa-users" style="font-size: 2rem; color: var(--secondary-color); margin-bottom: 1rem;"></i>
                <h3><?php echo $total_usuarios; ?></h3>
                <p>Usuarios Registrados</p>
            </div>
            <div class="card text-center">
                <i class="fas fa-briefcase" style="font-size: 2rem; color: var(--success-color); margin-bottom: 1rem;"></i>
                <h3><?php echo $ofertas_activas; ?></h3>
                <p>Ofertas Activas</p>
            </div>
            <div class="card text-center">
                <i class="fas fa-handshake" style="font-size: 2rem; color: var(--warning-color); margin-bottom: 1rem;"></i>
                <h3><?php echo $total_postulaciones; ?></h3>
                <p>Postulaciones Enviadas</p>
            </div>
            <div class="card text-center">
                <i class="fas fa-chart-line" style="font-size: 2rem; color: var(--accent-color); margin-bottom: 1rem;"></i>
                <h3><?php echo $total_ofertas; ?></h3>
                <p>Total de Ofertas</p>
            </div>
        </section>

        <!-- Ofertas Recientes -->
        <section class="fade-in">
            <h2><i class="fas fa-clock"></i> Ofertas Recientes</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Modalidad</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $ofertas_recientes = $conexion->query("SELECT * FROM ofertas ORDER BY fecha_inicio DESC LIMIT 5");
                        while ($oferta = $ofertas_recientes->fetch_assoc()) {
                            $estado = (strtotime($oferta['fecha_fin']) >= time()) ? 'Activa' : 'Expirada';
                            $estado_class = ($estado == 'Activa') ? 'success' : 'error';
                            echo "<tr>
                                    <td><strong>" . htmlspecialchars($oferta['titulo']) . "</strong></td>
                                    <td><span class='badge'>" . ucfirst($oferta['modalidad']) . "</span></td>
                                    <td>" . date('d/m/Y', strtotime($oferta['fecha_inicio'])) . "</td>
                                    <td>" . date('d/m/Y', strtotime($oferta['fecha_fin'])) . "</td>
                                    <td><span class='badge {$estado_class}'>{$estado}</span></td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Información del Sistema -->
        <section class="grid fade-in">
            <div class="card">
                <h3><i class="fas fa-info-circle"></i> ¿Qué es el Sistema de Vinculación?</h3>
                <p>Nuestro sistema conecta estudiantes universitarios con empresas y organizaciones para realizar prácticas profesionales, proyectos de vinculación y oportunidades de empleo.</p>
            </div>
            <div class="card">
                <h3><i class="fas fa-rocket"></i> Beneficios</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin: 0.5rem 0;"><i class="fas fa-check" style="color: var(--success-color);"></i> Experiencia práctica real</li>
                    <li style="margin: 0.5rem 0;"><i class="fas fa-check" style="color: var(--success-color);"></i> Networking profesional</li>
                    <li style="margin: 0.5rem 0;"><i class="fas fa-check" style="color: var(--success-color);"></i> Desarrollo de competencias</li>
                    <li style="margin: 0.5rem 0;"><i class="fas fa-check" style="color: var(--success-color);"></i> Oportunidades laborales</li>
                </ul>
            </div>
        </section>

        <!-- Footer -->
        <footer style="text-align: center; margin-top: 3rem; padding: 2rem; background: var(--white); border-radius: var(--border-radius); box-shadow: var(--shadow);">
            <p style="color: var(--text-light);">
                <i class="fas fa-university"></i> Universidad Fidélitas - Sistema de Vinculación Académica
            </p>
            <p style="color: var(--text-light); font-size: 0.9rem;">
                © 2024 Todos los derechos reservados | Desarrollado con <i class="fas fa-heart" style="color: var(--accent-color);"></i>
            </p>
        </footer>
    </div>

    <script>
        // Agregar animaciones cuando la página carga
        document.addEventListener('DOMContentLoaded', function() {
            const fadeElements = document.querySelectorAll('.fade-in');
            fadeElements.forEach((element, index) => {
                setTimeout(() => {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });
    </script>

    <style>
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease-out;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge.success {
            background: var(--success-color);
            color: white;
        }

        .badge.error {
            background: var(--accent-color);
            color: white;
        }

        .badge {
            background: var(--secondary-color);
            color: white;
        }
    </style>
</body>
</html>