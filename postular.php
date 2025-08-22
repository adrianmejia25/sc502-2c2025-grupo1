<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php?msg=login_required');
    exit();
}
if (($_SESSION['rol'] ?? '') !== 'estudiante') {
    header('Location: index.php?msg=solo_estudiante');
    exit();
}
include("conexion.php");
$conexion->set_charset("utf8mb4");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postular a Oferta - Sistema de Vinculación Académica</title>
    <link rel="stylesheet" href="estilos.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <!-- Header -->
        <header class="fade-in">
            <h1><i class="fas fa-graduation-cap"></i> Sistema de Vinculación Académica</h1>
            <p style="text-align: center; color: white; font-size: 1.2rem; margin-bottom: 2rem;">
                Universidad Fidélitas - Postular a Oferta
            </p>
        </header>

        <!-- Navegación -->
        <nav class="slide-in">
            <a href="index.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="registro.php"><i class="fas fa-user-plus"></i> Registro</a>
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
            <a href="publicar_oferta.php"><i class="fas fa-briefcase"></i> Publicar Oferta</a>
            <a href="postular.php"><i class="fas fa-paper-plane"></i> Postular</a>
            <a href="bitacora.php"><i class="fas fa-book"></i> Bitácora</a>
        </nav>

        <!-- Ofertas Disponibles -->
        <section class="fade-in">
            <h2><i class="fas fa-search"></i> Ofertas Disponibles</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Modalidad</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $ofertas_disponibles = $conexion->query("SELECT * FROM ofertas WHERE fecha_fin >= CURDATE() ORDER BY fecha_inicio ASC");
                        while ($oferta = $ofertas_disponibles->fetch_assoc()) {
                            $estado = 'Activa';
                            $descripcion_corta = strlen($oferta['descripcion']) > 100 ?
                                substr($oferta['descripcion'], 0, 100) . '...' :
                                $oferta['descripcion'];

                            echo "<tr>
                                    <td><strong>#{$oferta['id_oferta']}</strong></td>
                                    <td><strong>" . htmlspecialchars($oferta['titulo']) . "</strong></td>
                                    <td><span class='badge badge-" . strtolower($oferta['modalidad']) . "'>" . ucfirst($oferta['modalidad']) . "</span></td>
                                    <td>" . date('d/m/Y', strtotime($oferta['fecha_inicio'])) . "</td>
                                    <td>" . date('d/m/Y', strtotime($oferta['fecha_fin'])) . "</td>
                                    <td><span class='badge success'>{$estado}</span></td>
                                    <td>" . htmlspecialchars($descripcion_corta) . "</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Formulario de Postulación -->
        <div class="form-container fade-in">
            <h2><i class="fas fa-paper-plane"></i> Postular a una Oferta</h2>

            <?php
            if (isset($_POST['postular'])) {
                $id_usuario = (int) ($_SESSION['id_usuario'] ?? 0);
                $id_oferta = (int) ($_POST['id_oferta'] ?? 0);
                $estado = "pendiente";
                $fecha = date("Y-m-d");

                if (empty($id_usuario) || empty($id_oferta)) {
                    echo '<div class="message error"><i class="fas fa-exclamation-triangle"></i> Por favor, complete todos los campos.</div>';
                } else {
                    // Verificar que el usuario existe
                    $check_user = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE id_usuario = ? AND rol = 'estudiante'");
                    $check_user->bind_param("i", $id_usuario);
                    $check_user->execute();
                    $user_result = $check_user->get_result();

                    if ($user_result->num_rows == 0) {
                        echo '<div class="message error"><i class="fas fa-exclamation-triangle"></i> El ID de usuario no existe.</div>';
                    } else {
                        $user_data = $user_result->fetch_assoc();

                        // Verificar que la oferta existe y está activa
                        $check_oferta = $conexion->prepare("SELECT id_oferta, titulo, fecha_fin FROM ofertas WHERE id_oferta = ? AND fecha_fin >= CURDATE()");
                        $check_oferta->bind_param("i", $id_oferta);
                        $check_oferta->execute();
                        $oferta_result = $check_oferta->get_result();

                        if ($oferta_result->num_rows == 0) {
                            echo '<div class="message error"><i class="fas fa-exclamation-triangle"></i> La oferta no existe o ya expiró.</div>';
                        } else {
                            $oferta_data = $oferta_result->fetch_assoc();

                            // Verificar que no se haya postulado antes
                            $check_postulacion = $conexion->prepare("SELECT id_postulacion FROM postulaciones WHERE id_usuario = ? AND id_oferta = ?");
                            $check_postulacion->bind_param("ii", $id_usuario, $id_oferta);
                            $check_postulacion->execute();
                            $postulacion_result = $check_postulacion->get_result();

                            if ($postulacion_result->num_rows > 0) {
                                echo '<div class="message warning"><i class="fas fa-exclamation-triangle"></i> Ya te has postulado a esta oferta anteriormente.</div>';
                            } else {
                                $stmt = $conexion->prepare("INSERT INTO postulaciones(id_usuario, id_oferta, estado, fecha_postulacion) VALUES (?, ?, ?, ?)");
                                $stmt->bind_param("iiss", $id_usuario, $id_oferta, $estado, $fecha);

                                if ($stmt->execute()) {
                                    echo '<div class="message success"><i class="fas fa-check-circle"></i> Postulación enviada exitosamente para la oferta: <strong>' . htmlspecialchars($oferta_data['titulo']) . '</strong></div>';
                                } else {
                                    echo '<div class="message error"><i class="fas fa-exclamation-triangle"></i> Error al enviar la postulación.</div>';
                                }
                            }
                        }
                    }
                }
            }
            ?>

            <form method="post" class="fade-in">
                <div class="form-group">
                    <label for="id_usuario"><i class="fas fa-user"></i> ID de Estudiante</label>
                    <input type="text" id="id_usuario" value="<?php echo (int) ($_SESSION['id_usuario'] ?? 0); ?>"
                        readonly>
                    <small style="color: var(--text-light); font-size: 0.8rem;">
                        <i class="fas fa-info-circle"></i> El sistema usa automáticamente tu ID de la sesión.
                    </small>
                </div>
                <label for="id_oferta"><i class="fas fa-briefcase"></i> ID de la Oferta</label>
                <input type="number" name="id_oferta" id="id_oferta" placeholder="ID de la oferta a postular" required
                    min="1">
        </div>

        <button type="submit" name="postular" class="btn-primary">
            <i class="fas fa-paper-plane"></i> Postular
        </button>
        </form>

        <!-- Footer -->
        <footer
            style="text-align: center; margin-top: 3rem; padding: 2rem; background: var(--white); border-radius: var(--border-radius); box-shadow: var(--shadow);">
            <p style="color: var(--text-light);">
                <i class="fas fa-university"></i> Universidad Fidélitas - Sistema de Vinculación Académica
            </p>
            <p style="color: var(--text-light); font-size: 0.9rem;">
                © 2025 Todos los derechos reservados | Desarrollado con <i class="fas fa-heart"
                    style="color: var(--accent-color);"></i>
            </p>
        </footer>

    </div>