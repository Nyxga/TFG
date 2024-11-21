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
    session_start();

    if (!isset($_SESSION['email'])) {
        header('Location: index.php');
        exit();
    }

    require 'conexion.php';

    $empleados = [];
    try {
        $sql = "SELECT NOMBRE, APELLIDOS, EMAIL, FOTO FROM EMPLEADOS";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger" role="alert">Error al cargar empleados: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    ?>

    <header>
        <a href="./inicio.php">
            <h6><i class="bi bi-house-fill"></i> Volver a inicio</h6>
        </a>
    </header>

    <section>
        <h1 class="fs-3">Lista de empleados</h1>
    </section>

    <article>
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($empleados) > 0): ?>
                    <?php foreach ($empleados as $empleado): ?>
                        <tr>
                            <td>
                                <?php if (!empty($empleado['FOTO'])): ?>
                                    <img src="<?php echo htmlspecialchars($empleado['FOTO']); ?>" alt="Foto de <?php echo htmlspecialchars($empleado['NOMBRE']); ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                                <?php else: ?>
                                    <img src="./images/default-avatar.png" alt="Foto no disponible" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($empleado['NOMBRE']); ?></td>
                            <td><?php echo htmlspecialchars($empleado['APELLIDOS']); ?></td>
                            <td><?php echo htmlspecialchars($empleado['EMAIL']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No hay empleados registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </article>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>