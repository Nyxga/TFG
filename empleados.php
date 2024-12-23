<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados</title>
    <link rel="stylesheet" href="./css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        header {
            width: 100%;
            justify-content: flex-start;
            position: unset;
            padding: 40px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body id="empleados">
    <?php
    include 'listar_empleados.php';
    ?>


    <header class="p-4 mb-0">
        <a href="./admin/inicio.php">
            <h6 style="color: #0c0e66;"><i class="bi bi-house-fill"></i> Volver a inicio</h6>
        </a>
    </header>

    <main class="p-4">
        <section class="d-flex justify-content-center">
            <h1 class="fs-3 mt-4">Lista de empleados</h1>
        </section>

        <hr>

        <article>
            <div class="table-responsive">
                <div>
                    <form method="GET" action="" class="d-flex justify-content-center">
                        <input type="text" name="busqueda" class="form-control w-auto text-center" placeholder="Buscar" value="<?php echo htmlspecialchars($busqueda); ?>">
                    </form>
                </div>
                <br>
                <table class="table table-hover align-middle">
                    <tbody>
                        <?php if (count($empleados) > 0): ?>
                            <?php foreach ($empleados as $empleado): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($empleado['FOTO'])): ?>
                                            <img src="<?php echo htmlspecialchars($empleado['FOTO']); ?>" alt="Foto de <?php echo htmlspecialchars($empleado['NOMBRE']); ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;" id="img_tabla">
                                        <?php else: ?>
                                            <img src="../img/foto_default.svg" alt="Foto no disponible" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($empleado['NOMBRE'] . ' ' .$empleado['APELLIDOS']); ?></td>
                                    <td>
                                        <form method="GET" action="../actualizar_perfil.php" style="display: inline;">
                                            <input type="hidden" name="usuario" value="<?php echo $empleado['NUMERO_EMPLEADO']; ?>">
                                            <button type="submit" class="btn btn-chat">
                                            <i class="bi bi-info-circle"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No hay empleados registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>
    </main>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>