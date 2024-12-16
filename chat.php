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
</head>

<body id="chat">
    <?php
    include 'listar_empleados.php';
    include 'listar_mensajes.php';
    ?>

    <header class="d-flex justify-content-between align-items-center mb-4 p-4">
        <a href="./inicio.php">
            <h6 style="color: #0c0e66;"><i class="bi bi-house-fill"></i> Volver a inicio</h6>
        </a>
        <div>
            <img src="<?php echo ($foto_url) ?>" alt="Foto de perfil" id="foto_usuario">
            <span class="px-2"><?php echo $nombre . ' ' . $apellidos ?></span>
        </div>
    </header>

    <main>
        <article>
            <div id="contenedor_receptor">
                <?php
                echo '<img id="foto_usuario" src="' . $foto_seleccionada . '" alt="Foto de ' . $usuario_receptor . '"><span>' . $nombre_receptor . ' ' . $apellidos_receptor . '</span>';
                ?>
            </div>
            <div class="linea d-flex">
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

                                    // Determinar qué nombre mostrar
                                    $nombre_mostrar = $es_mi_mensaje ? ' ' : $nombre_receptor . ' ' . $apellidos_receptor;
                                    $hora_formateada = date('H:i', strtotime($mensaje['fecha']));

                                    // Clase CSS según si el mensaje es del emisor o receptor
                                    $clase_mensaje = $es_mi_mensaje ? 'mi-mensaje' : 'mensaje-empleado';

                                    echo '<tr>';

                                    // Columna de la foto (para mensajes recibidos) o vacía (para mensajes enviados)
                                    if (!$es_mi_mensaje) {
                                        echo '<td id="foto-chat">';
                                        // Verifica si el usuario tiene una foto asignada, si no, muestra la foto predeterminada
                                        $foto_usuario = !empty($empleado['FOTO']) ? $empleado['FOTO'] : './img/foto_default.svg';
                                        echo '<img src="' . htmlspecialchars($foto_seleccionada) . '" alt="Foto de ' . htmlspecialchars($nombre_mostrar) . '" id="foto_usuario">';
                                        echo '</td>';
                                    } else {
                                        echo '<td id="foto-chat"></td>';
                                    }


                                    // Columna del mensaje con el nombre encima
                                    echo '<td class="mensaje ' . $clase_mensaje . '">';
                                    echo '<div><strong>' . htmlspecialchars($nombre_mostrar . ' ') . '</strong><small>' . htmlspecialchars($hora_formateada) . '</small></div>'; // Hora
                                    echo '<p>' . htmlspecialchars($mensaje['mensaje']) . '</p>'; // Contenido del mensaje
                                    echo '</td>';

                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="2">No hay mensajes en este chat.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </article>

        <article>
            <table class="table" id="tabla_form_mensaje">
                <tbody>
                    <tr>
                        <td></td>
                        <td>
                            <form action="" method="POST">
                                <input type="text" name="mensaje" class="form-control" placeholder="Escribe un mensaje..." autofocus required>
                                <button type="submit" class="btn btn-primary">Enviar</button>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
        </article>
    </main>


    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const contenedorChat = document.querySelector(".tabla-chat");
            contenedorChat.scrollTop = contenedorChat.scrollHeight;
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>