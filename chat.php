<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="./css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            padding: 20px;
        }

        header {
            width: 100%;
            justify-content: flex-start;
            position: unset;
            padding: 0;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <?php
    include 'listar_empleados.php';
    ?>

    <header>
        <a href="./inicio.php">
            <h6 style="color: #0c0e66;"><i class="bi bi-house-fill"></i> Volver a inicio</h6>
        </a>

        <div>
            <img src="<?php echo !empty($foto_url) ? $foto_url : $foto_predeterminada; ?>" alt="Foto de perfil" id="foto_usuario">
            <span><?php echo $nombre . ' ' . $apellidos; ?></span>
        </div>
    </header>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>