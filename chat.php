<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="./css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            padding: 20px;
        }

        header {
            width: 100%;
            justify-content: space-between;
            position: unset;
            padding: 0;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <?php
    include 'listar_empleados.php';
    include 'listar_mensajes.php';

    $foto_seleccionada = isset($_GET['foto']) ? $_GET['foto'] : './img/foto_default.svg';
    ?>

    <header>
        <a href="./inicio.php">
            <h6 style="color: #0c0e66;"><i class="bi bi-house-fill"></i> Volver a inicio</h6>
        </a>
        <div>
            <img src="<?php echo !empty($foto_url) ? $foto_url : $foto_predeterminada; ?>" alt="Foto de perfil" id="foto_usuario">
            <span><?php echo $nombre . ' ' . $apellidos ?></span>
        </div>
    </header>

    <section class="mb-4">
        <h1 class="fs-3">Chat</h1>
    </section>

    <article>
        <div class="linea">
            <!-- Tabla de usuarios -->
            <div class="tabla-usuarios">
                <form method="GET" action="">
                    <table class="table table-hover">
                        <tbody>
                            <?php if (count($empleados) > 0): ?>
                                <?php foreach ($empleados as $empleado): ?>
                                    <tr>
                                        <td>
                                            <form method="GET" action="chat.php">
                                                <input type="hidden" name="usuario" value="<?php echo htmlspecialchars($empleado['NUMERO_EMPLEADO']); ?>">
                                                <input type="hidden" name="foto" value="<?php echo !empty($empleado['FOTO']) ? htmlspecialchars($empleado['FOTO']) : './img/foto_default.svg'; ?>">
                                                <button type="submit" class="btn btn-link text-decoration-none">
                                                    <img src="<?php echo !empty($empleado['FOTO']) ? htmlspecialchars($empleado['FOTO']) : './img/foto_default.svg'; ?>" alt="Foto de <?php echo htmlspecialchars($empleado['NOMBRE']); ?>" id="foto_usuario">
                                                    <?php echo htmlspecialchars($empleado['NOMBRE'] . ' ' . $empleado['APELLIDOS']); ?>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td>No hay usuarios disponibles.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </form>
            </div>

            <!-- Contenedor del chat -->
            <div class="tabla-chat">
                <table id="contenedor_chat" class="table">
                    <tbody>
                        <?php
                        // Mostrar los mensajes entre el emisor y el receptor
                        if (!empty($mensajes)) {
                            foreach ($mensajes as $mensaje) {
                                // Verificar quién es el emisor del mensaje
                                $es_mi_mensaje = ($mensaje['emisor'] == $usuario_emisor);
                                $nombre_emisor = ($es_mi_mensaje) ? '' : $empleado['NOMBRE'] . ' ' . $empleado['APELLIDOS'] . ' ';
                                $foto_emisor = $es_mi_mensaje ? $foto_seleccionada : './img/foto_default.svg';
                                $hora_formateada = date('H:i', strtotime($mensaje['fecha']));

                                $clase_mensaje = $es_mi_mensaje ? 'mi-mensaje' : 'mensaje-empleado';
                                echo '<tr>';

                                // Mostrar la foto del receptor (solo si no es el emisor)
                                if (!$es_mi_mensaje) {
                                    echo '<td id="foto-chat"><img src="' . htmlspecialchars($foto_emisor) . '" alt="Foto de ' . htmlspecialchars($nombre_emisor) . '" id="foto_usuario"></td>';
                                } else {
                                    // Para los mensajes del emisor, agregar una celda vacía para mantener la alineación
                                    echo '<td id="foto-chat"></td>';
                                }

                                // Mostrar el contenido del mensaje
                                echo '<td class="mensaje ' . $clase_mensaje . '">';
                                echo '<strong>' . htmlspecialchars($nombre_emisor) . '</strong>';
                                echo '<small>' . htmlspecialchars($hora_formateada) . '</small>';
                                echo '<p>' . htmlspecialchars($mensaje['mensaje']) . '</p>';
                                echo '</td>';
                                echo '</tr>';
                            }
                            echo
                            '<tr>
                                <td></td>
                                <td id="formulario_mensaje">
                                    <form action="" method="POST">
                                        <input type="text" name="mensaje" class="form-control" placeholder="Escribe un mensaje..." required>
                                        <button type="submit" class="btn btn-primary">Enviar</button>
                                    </form>
                                </td>
                            </tr>';
                        } else {
                            echo '<tr><td colspan="2">No hay mensajes en este chat.</td></tr>';
                        }
                        ?>

                    </tbody>
                </table>

                <!-- Formulario de envío -->

            </div>
        </div>
    </article>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>