<?php
session_start();
include("conexion.php");

$mensaje = '';
$tipo_mensaje = '';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['guardar'])) {
    $id_postulacion = filter_var($_POST['id_postulacion'], FILTER_SANITIZE_NUMBER_INT);
    $observaciones = filter_var($_POST['observaciones'], FILTER_SANITIZE_STRING);
    $horas_realizadas = filter_var($_POST['horas_realizadas'], FILTER_SANITIZE_NUMBER_INT);
    $fecha = $_POST['fecha'];
    
    // Validaciones
    if (empty($id_postulacion) || empty($horas_realizadas) || empty($fecha)) {
        $mensaje = 'Por favor, completa todos los campos obligatorios.';
        $tipo_mensaje = 'error';
    } elseif ($horas_realizadas < 0 || $horas_realizadas > 24) {
        $mensaje = 'Las horas realizadas deben estar entre 0 y 24.';
        $tipo_mensaje = 'error';
    } elseif (strtotime($fecha) > time()) {
        $mensaje = 'La fecha no puede ser futura.';
        $tipo_mensaje = 'error';
    } else {
        // Verificar que la postulación existe y pertenece al usuario
        $stmt_check = $conexion->prepare("SELECT p.*, o.titulo FROM postulaciones p 
                                          JOIN ofertas o ON p.id_oferta = o.id_oferta 
                                          WHERE p.id_postulacion = ? AND p.id_usuario = ?");
        $stmt_check->bind_param("ii", $id_postulacion, $_SESSION['id_usuario']);
        $stmt_check->execute();
        $resultado_check = $stmt_check->get_result();
        
        if ($resultado_check->num_rows == 0) {
            $mensaje = 'No tienes permisos para crear bitácora para esta postulación.';
            $tipo_mensaje = 'error';
        } else {
            // INSERT correcto en bitacoras (sin id_usuario)
            $stmt = $conexion->prepare("INSERT INTO bitacoras (id_postulacion, observaciones, horas_realizadas, fecha) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isis", $id_postulacion, $observaciones, $horas_realizadas, $fecha);
            if ($stmt->execute()) {
                $mensaje = 'Bitácora guardada exitosamente.';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al guardar: ' . $stmt->error;
                $tipo_mensaje = 'error';
            }
        }
    }
}

// Obtener las postulaciones del usuario
$postulaciones_usuario = $conexion->prepare("SELECT p.*, o.titulo FROM postulaciones p 
                                            JOIN ofertas o ON p.id_oferta = o.id_oferta 
                                            WHERE p.id_usuario = ? AND p.estado IN ('aceptada', 'pendiente')");
$postulaciones_usuario->bind_param("i", $_SESSION['id_usuario']);
$postulaciones_usuario->execute();
$postulaciones_result = $postulaciones_usuario->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácora de Postulación - Sistema de Vinculación Académica</title>
    <link rel="stylesheet" href="estilos.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="fade-in">
            <h1><i class="fas fa-book"></i> Bitácora de Postulación</h1>
            <p style="text-align: center; color: white; font-size: 1.1rem; margin-bottom: 2rem;">
                Registra tus actividades y progreso
            </p>
        </header>

        <!-- Navegación -->
        <nav class="slide-in">
            <a href="index.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="postular.php"><i class="fas fa-paper-plane"></i> Postular</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </nav>

        <!-- Formulario de Bitácora -->
        <div class="form-container fade-in">
            <h2><i class="fas fa-plus-circle"></i> Nueva Entrada de Bitácora</h2>
            
            <?php if (!empty($mensaje)): ?>
                <div class="message <?php echo $tipo_mensaje; ?>">
                    <i class="fas fa-<?php echo $tipo_mensaje == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <form method="post" id="bitacoraForm">
                <div class="form-group">
                    <label for="id_postulacion">
                        <i class="fas fa-clipboard-list"></i> Postulación
                    </label>
                    <select name="id_postulacion" id="id_postulacion" required>
                        <option value="">Selecciona una postulación</option>
                        <?php while ($postulacion = $postulaciones_result->fetch_assoc()): ?>
                            <option value="<?php echo $postulacion['id_postulacion']; ?>">
                                <?php echo htmlspecialchars($postulacion['titulo']); ?> 
                                (ID: <?php echo $postulacion['id_postulacion']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fecha">
                        <i class="fas fa-calendar-alt"></i> Fecha
                    </label>
                    <input 
                        type="date" 
                        id="fecha"
                        name="fecha" 
                        required
                        max="<?php echo date('Y-m-d'); ?>"
                        value="<?php echo date('Y-m-d'); ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="horas_realizadas">
                        <i class="fas fa-clock"></i> Horas Realizadas
                    </label>
                    <input 
                        type="number" 
                        id="horas_realizadas"
                        name="horas_realizadas" 
                        placeholder="Número de horas" 
                        required
                        min="0"
                        max="24"
                        step="0.5"
                    >
                </div>

                <div class="form-group">
                    <label for="observaciones">
                        <i class="fas fa-sticky-note"></i> Observaciones
                    </label>
                    <textarea 
                        id="observaciones"
                        name="observaciones" 
                        placeholder="Describe las actividades realizadas, aprendizajes obtenidos, dificultades encontradas, etc."
                        rows="5"
                    ></textarea>
                </div>

                <button type="submit" name="guardar" id="guardarBtn">
                    <i class="fas fa-save"></i> Guardar Bitácora
                </button>
            </form>
        </div>

        <!-- Listado de Bitácoras -->
        <section class="fade-in">
            <h2><i class="fas fa-list"></i> Mis Bitácoras</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Postulación</th>
                            <th>Fecha</th>
                            <th>Horas</th>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $bitacoras = $conexion->prepare("SELECT b.*, p.id_postulacion, o.titulo 
                                                        FROM bitacoras b 
                                                        JOIN postulaciones p ON b.id_postulacion = p.id_postulacion 
                                                        JOIN ofertas o ON p.id_oferta = o.id_oferta 
                                                        WHERE p.id_usuario = ?
                                                        ORDER BY b.fecha DESC");
                        $bitacoras->bind_param("i", $_SESSION['id_usuario']);
                        $bitacoras->execute();
                        $resultado_bitacoras = $bitacoras->get_result();
                        
                        $total_horas = 0;
                        while ($fila = $resultado_bitacoras->fetch_assoc()) {
                            $total_horas += $fila['horas_realizadas'];
                            echo "<tr>
                                    <td><strong>#{$fila['id_bitacora']}</strong></td>
                                    <td>" . htmlspecialchars($fila['titulo']) . "</td>
                                    <td>" . date('d/m/Y', strtotime($fila['fecha'])) . "</td>
                                    <td><span class='badge'>{$fila['horas_realizadas']}h</span></td>
                                    <td>" . htmlspecialchars(substr($fila['observaciones'], 0, 100)) . 
                                    (strlen($fila['observaciones']) > 100 ? '...' : '') . "</td>
                                    <td>
                                        <button onclick='verDetalles({$fila['id_bitacora']})' class='btn-small'>
                                            <i class='fas fa-eye'></i>
                                        </button>
                                    </td>
                                  </tr>";
                        }
                        
                        if ($resultado_bitacoras->num_rows == 0) {
                            echo "<tr><td colspan='6' style='text-align: center; color: var(--text-light);'>
                                    <i class='fas fa-inbox'></i> No hay bitácoras registradas
                                  </td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($total_horas > 0): ?>
                <div class="card" style="margin-top: 2rem;">
                    <div style="text-align: center;">
                        <h3><i class="fas fa-chart-pie"></i> Resumen de Horas</h3>
                        <p style="font-size: 2rem; color: var(--secondary-color); font-weight: bold;">
                            <?php echo $total_horas; ?> horas
                        </p>
                        <p style="color: var(--text-light);">Total de horas registradas</p>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <!-- Modal para ver detalles -->
    <div id="modalDetalles" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3><i class="fas fa-info-circle"></i> Detalles de la Bitácora</h3>
            <div id="contenidoModal">
                <!-- Contenido dinámico -->
            </div>
        </div>
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

            // Validación del formulario
            const form = document.getElementById('bitacoraForm');
            form.addEventListener('submit', function(e) {
                const horas = document.getElementById('horas_realizadas').value;
                const fecha = document.getElementById('fecha').value;
                
                if (horas < 0 || horas > 24) {
                    e.preventDefault();
                    alert('Las horas deben estar entre 0 y 24.');
                    return;
                }
                
                if (new Date(fecha) > new Date()) {
                    e.preventDefault();
                    alert('La fecha no puede ser futura.');
                    return;
                }
                
                // Mostrar loading
                const btn = document.getElementById('guardarBtn');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
                btn.disabled = true;
            });
        });

        // Función para ver detalles
        function verDetalles(id) {
            // Implementar modal con detalles de la bitácora
            alert('Funcionalidad de detalles - ID: ' + id);
        }

        // Modal
        const modal = document.getElementById('modalDetalles');
        const span = document.getElementsByClassName('close')[0];

        span.onclick = function() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
    <!-- Footer -->
        <footer style="text-align: center; margin-top: 3rem; padding: 2rem; background: var(--white); border-radius: var(--border-radius); box-shadow: var(--shadow);">
            <p style="color: var(--text-light);">
                <i class="fas fa-university"></i> Universidad Fidélitas - Sistema de Vinculación Académica
            </p>
            <p style="color: var(--text-light); font-size: 0.9rem;">
                © 2025 Todos los derechos reservados | Desarrollado con <i class="fas fa-heart" style="color: var(--accent-color);"></i>
            </p>
        </footer>

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
            background: var(--secondary-color);
            color: white;
        }

        .btn-small {
            padding: 0.5rem;
            background: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-small:hover {
            background: var(--primary-color);
            transform: scale(1.1);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: var(--white);
            margin: 15% auto;
            padding: 20px;
            border-radius: var(--border-radius);
            width: 80%;
            max-width: 500px;
            position: relative;
        }

        .close {
            color: var(--text-light);
            float: right;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            right: 15px;
            top: 10px;
            cursor: pointer;
        }

        .close:hover {
            color: var(--accent-color);
        }
    </style>
</body>
</html>