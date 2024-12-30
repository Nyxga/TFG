<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body id="empleados">
    <?php
    include '../listar_empleados.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_empleado'])) {
        $numero_empleado = $_POST['numero_empleado'];
        try {
            $stmt = $conexion->prepare("DELETE FROM empleados WHERE numero_empleado = ?");
            $stmt->execute([$numero_empleado]);

            $_SESSION['mensaje'] = "Empleado " . $empleado['username'] . " eliminado correctamente.";
        } catch (PDOException $e) {
            $_SESSION['mensaje'] = "Error al eliminar el empleado: " . htmlspecialchars($e->getMessage());
        }

        header('Location: ./empleados.php');
        exit();
    }
    ?>

    <header class="p-4 mb-0">
        <a href="./inicio.php">
            <h6 class="text-dark"><i class="bi bi-house-fill text-dark"></i> Volver a inicio</h6>
        </a>
    </header>

    <main class="p-4">
        <section class="d-flex justify-content-center">
            <h1 class="fs-3 mt-4">Lista de empleados</h1>
        </section>

        <hr>

        <?php
        if (isset($_SESSION['mensaje'])) {
            echo '<div class="alert alert-success text-center">' . htmlspecialchars($_SESSION['mensaje']) . '</div>';
            unset($_SESSION['mensaje']);
        }
        ?>

        <article>
            <div class="table-responsive">
                <div>
                    <form method="GET" action="" class="d-flex justify-content-center p-4">
                        <input type="text" name="busqueda" class="form-control w-auto text-center" placeholder="Nombre" value="<?php echo htmlspecialchars($busqueda); ?>">
                        <button type="submit" name="buscar" class="btn btn-primary mx-4">
                            <i class="bi bi-search"></i><span id="span_buscar"> Buscar</span>
                        </button>
                        <button type="button" class="btn btn-danger" onclick="window.location.href='./empleados.php'">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </form>
                </div>
                <br>
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <td>Foto</td>
                            <td>Nombre</td>
                            <td colspan="3">Usuario</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($empleados) > 0): ?>
                            <?php foreach ($empleados as $empleado): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty('.' . $empleado['foto'])): ?>
                                            <img src="<?php echo htmlspecialchars('.' . $empleado['foto']); ?>" alt="Foto de <?php echo htmlspecialchars($empleado['nombre']); ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;" id="img_tabla">
                                        <?php else: ?>
                                            <img src="../img/foto_perfil/foto_default.svg" alt="Foto no disponible" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellidos']); ?></td>
                                    <td><?php echo htmlspecialchars($empleado['username']); ?></td>
                                    <td>
                                        <form method="GET" action="../actualizar_perfil.php" style="display: inline;">
                                            <input type="hidden" name="usuario" value="<?php echo $empleado['numero_empleado']; ?>">
                                            <button type="submit" class="btn btn-chat">
                                                <i class="bi bi-info-circle"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <form method="POST" action="" style="display: inline;">
                                            <input type="hidden" name="numero_empleado" value="<?php echo $empleado['numero_empleado']; ?>">
                                            <button type="submit" name="eliminar_empleado" class="btn btn-danger"
                                                onclick="return confirm('¿Estás seguro de que quieres eliminar al empleado <?php echo htmlspecialchars($empleado['username']); ?>?');">
                                                <i class="bi bi-trash3-fill"></i>
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