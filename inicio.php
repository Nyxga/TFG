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

<body>
    <main>
        <?php
        include 'listar_empleados.php';
        ?>

        <header>
            <div class="dropdown">
                <button class="btn" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?php echo !empty($foto_url) ? $foto_url : $foto_predeterminada; ?>" alt="Foto de perfil" id="foto_usuario">
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item text-dark" href="#" id="cambiarFoto">Actualizar foto de perfil <i class="bi bi-pencil-square"></i></a>
                        <form action="fotoperfil.php" method="post" enctype="multipart/form-data" style="display: none;" id="form-cambiar-foto">
                            <input type="file" name="image" accept="image/*" id="input-foto" onchange="document.getElementById('form-cambiar-foto').submit();">
                        </form>
                    </li>
                    <li>
                        <form method="POST" action="">
                            <button type="submit" name="logout" class="dropdown-item" style="border: none; background: none;">
                                Cerrar sesión <i class="bi bi-box-arrow-right h6"></i>
                            </button>
                        </form>
                    </li>
                </ul>
                <span><?php echo $nombre . ' ' . $apellidos; ?></span>
            </div>
        </header>

        <aside>
            <h1 class="fs-1 mb-20 text-light">Orion</h1>
            <nav>
                <ul>
                    <li><a href="./empleados.php"><i class="bi bi-people-fill me-3"></i>Empleados</a></li>
                    <li><a href="./chat.php"><i class="bi bi-chat-dots-fill me-3"></i></i>Chat</a></li>
                    <li><a href="#">Permisos</a></li>
                    <li><a href="#">Turnos</a></li>
                    <li><a href="#">Nóminas</a></li>
                    <li><a href="#">Documentos</a></li>
                </ul>
            </nav>
        </aside>


    </main>





    <!-- NO TOCAR -->
    <script>
        document.getElementById('cambiarFoto').addEventListener('click', function() {
            document.getElementById('input-foto').click();
        });
    </script>


    <script>
        if (document.getElementById('alert-success')) {
            setTimeout(function() {
                var alerta = new bootstrap.Alert(document.getElementById('alerta-success'));
                alerta.close();
            }, 5000);
        }
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>