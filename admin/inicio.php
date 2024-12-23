<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        @media (max-width: 480px) {
            .table tr {
                display: flex;
                flex-direction: column;
            }

            .table td {
                display: block;
                width: 100%;
                text-align: left;
            }
        }
    </style>
</head>

<body id="inicio">
    <?php
    include '../log_horario.php';

    if (isset($_POST['logout'])) {
        session_start();
        session_unset();
        session_destroy();

        header('Location: ../index.php');
        exit();
    }
    ?>

    <header class="d-inline-flex justify-content-end align-items-center p-4 position-fixed top-0 end-0 w-100">
        <div class="dropdown">
            <button class="dropbtn">
                <img src="<?php echo !empty($foto_url) ? '.' . $foto_url : '.' . $foto_predeterminada; ?>" alt="Foto de perfil" id="foto_usuario">
            </button>

            <div class="dropdown-content">
                <li>
                    <a class="d-flex text-dark" href="../actualizar_perfil.php">Actualizar perfil<i class="bi bi-pencil-square px-1"></i></a>
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

    <main class="p-4">
        <!-- <section class="d-flex justify-content-center text-center mt-4 mb-4">
            <h1>
                <div id="reloj"></div>
            </h1>
        </section> -->


        <section id="titulo_historial" class="d-flex mb-4">
            <h2>Panel administrativo de empleados</h2>
        </section>

        <hr>

        <article>
            <div class="table-responsive">
                <table class="table table-bordered" style="border-collapse: separate !important;">
                    <tr>
                        <td>
                            <h5>
                                <a href="../empleados.php" class="d-block text-decoration-none text-dark">
                                    <i class="bi bi-people-fill"></i>
                                    <span>Empleados</span>
                                    <!-- <?php echo $total_empleados ?> -->
                                </a>
                            </h5>
                        </td>
                        <td>
                            <h5>
                                <a href="#enlace2" class="d-block text-decoration-none text-dark">
                                    Enlace 2
                                </a>
                            </h5>
                        </td>
                        <td>
                            <h5>
                                <a href="#enlace3" class="d-block text-decoration-none text-dark">
                                    Enlace 3
                                </a>
                            </h5>
                        </td>
                        <td>
                            <h5>
                                <a href="#enlace4" class="d-block text-decoration-none text-dark">
                                    Enlace 4
                                </a>
                            </h5>
                        </td>
                    </tr>
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