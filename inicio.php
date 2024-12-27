<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="./css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body id="inicio">
    <?php
    include './log_horario.php';
    ?>


    <header class="d-inline-flex justify-content-end align-items-center p-4 position-fixed top-0 end-0 w-100">
        <div class="dropdown">
            <button class="dropbtn">
                <img src="<?php echo !empty($foto_url) ? $foto_url : $foto_predeterminada; ?>" alt="Foto de perfil" id="foto_usuario">
            </button>
            <div class="dropdown-content">
                <li>
                    <a class="d-flex text-dark" href="#" onclick="location.href='./actualizar_perfil.php?usuario=' + <?php echo $_SESSION['numero_empleado']; ?>">Actualizar perfil<i class="bi bi-pencil-square px-1"></i></a>
                </li>
                <li>
                    <form method="POST" action="">
                        <button type="submit" name="logout" class="dropdown-item">
                            Cerrar sesión <i class="bi bi-box-arrow-right px-1"></i>
                        </button>
                    </form>
                </li>
            </div>
        </div>
        <span><?php echo $nombre . ' ' . $apellidos; ?></span>
    </header>

    <main>
        <section class="d-flex justify-content-center text-center mt-4 mb-4">
            <h1>
                <div id="reloj"></div>
            </h1>
        </section>

        <article id="control_horario" class="d-flex justify-content-center">
            <form action="log_horario.php" method="POST" id="form-control-horario">
                <table>
                    <tr>
                        <td>
                            <button type="submit" name="tipo" value="Entrada" class="btn btn-success" onclick="return confirmarFichaje('Entrada')">⏰ Entrada</button>
                        </td>
                        <td>
                            <button type="submit" name="tipo" value="Salida" class="btn btn-danger" onclick="return confirmarFichaje('Salida')">🚪 Salida</button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button type="submit" name="tipo" value="Inicio Descanso" class="btn btn-success" onclick="return confirmarFichaje('Inicio Descanso')">☕ Inicio descanso</button>
                        </td>
                        <td>
                            <button type="submit" name="tipo" value="Fin Descanso" class="btn btn-danger" onclick="return confirmarFichaje('Fin Descanso')">🔄 Fin descanso</button>
                        </td>
                    </tr>
                </table>
            </form>
        </article>

        <section id="titulo_historial" class="d-flex mt-4 mb-4">
            <h2>Historial de fichaje</h2>
        </section>

        <hr>

        <section class="d-flex mt-4 mb-4">
            <div>
                <span><i class="bi bi-funnel-fill"></i> Filtrar fecha y tipo</span>
                <br>
                <form method="POST" action="inicio.php" id="form_filtrar">
                    <div class="d-flex mt-2">
                        <input id="filtrar_fecha" type="date" name="filtrar_fecha" class="rounded border text-center" style="height: auto;">
                        <select name="filtrar_tipo" class="form-select mx-2 w-50">
                            <option value="" selected disabled hidden>Tipo</option>
                            <option value="Entrada">Entrada</option>
                            <option value="Salida">Salida</option>
                            <option value="Inicio Descanso">Inicio descanso</option>
                            <option value="Fin Descanso">Fin descanso</option>
                        </select>
                    </div>
                    <div class="d-flex mt-2">
                        <button type="submit" name="buscar" class="btn btn-primary">
                            <i class="bi bi-search"></i><span> Buscar</span>
                        </button> 
                    </div>
                </form>
                <div class="d-inline">
                    <form method="POST" action="generar_excel.php" class="d-flex mt-2">
                        <input type="hidden" name="filtrar_fecha" value="<?php echo isset($_POST['filtrar_fecha']) ? $_POST['filtrar_fecha'] : ''; ?>">
                        <input type="hidden" name="filtrar_tipo" value="<?php echo isset($_POST['filtrar_tipo']) ? $_POST['filtrar_tipo'] : ''; ?>">
                        <button type="submit" class="btn btn-success" formtarget="_blank">
                            <i class="bi bi-file-earmark-excel"></i> Generar EXCEL
                        </button>
                    </form>
                </div>
            </div>
        </section>


        <article>
            <div class="table-responsive">
                <table id="tabla_fichajes" class="table table-hover">
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <th>Tipo</th>
                            <th>Dispositivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($empleados as $empleado) : ?>
                            <tr>
                                <?php
                                $fecha = new DateTime($empleado['fecha_hora']);
                                $fecha_formateada = $fecha->format('d/m/Y G:i:s');

                                // Definir las clases en función del tipo de fichaje
                                $clase_tipo = '';
                                if ($empleado['tipo_fichaje'] == 'Entrada') {
                                    $clase_tipo = 'table-success';
                                } elseif ($empleado['tipo_fichaje'] == 'Salida') {
                                    $clase_tipo = 'table-danger';
                                } elseif ($empleado['tipo_fichaje'] == 'Inicio descanso') {
                                    $clase_tipo = 'table-warning';
                                } elseif ($empleado['tipo_fichaje'] == 'Fin descanso') {
                                    $clase_tipo = 'table-warning';
                                }
                                ?>
                                <td><?php echo $nombre; ?></td>
                                <td><?php echo $fecha_formateada; ?></td>
                                <td class="<?php echo $clase_tipo; ?>"><?php echo $empleado['tipo_fichaje']; ?></td>
                                <td><?php echo $empleado['dispositivo']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>

    </main>


    <!-- NO TOCAR -->
    <script>
        function actualizarFechaHora() {
            const ahora = new Date();
            const fecha = ahora.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            const hora = ahora.toLocaleTimeString('es-ES', {
                timeZone: 'Europe/Madrid',
                hour12: false
            });

            document.getElementById('reloj').innerHTML = hora + '<br>' + fecha;
        }

        actualizarFechaHora();

        setInterval(actualizarFechaHora, 1000);
    </script>

    <script>
        if (document.getElementById('alert-success')) {
            setTimeout(function() {
                var alerta = new bootstrap.Alert(document.getElementById('alerta-success'));
                alerta.close();
            }, 5000);
        }
    </script>

    <script>
        window.addEventListener('beforeunload', function() {
            navigator.sendBeacon('cerrar_sesion.php');
        });
    </script>

    <script>
        function confirmarFichaje(tipo) {
            return confirm('¿Estás seguro de que quieres registrar la acción: ' + tipo + '?');
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>