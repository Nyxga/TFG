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

    // Inicializa variables
    $empleados = [];
    $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

    try {
        // Consulta SQL con filtro de búsqueda
        $sql = "SELECT NOMBRE, APELLIDOS, EMAIL, FOTO FROM EMPLEADOS";
        if (!empty($busqueda)) {
            $sql .= " WHERE NOMBRE LIKE :busqueda OR APELLIDOS LIKE :busqueda OR EMAIL LIKE :busqueda";
        }
        $sql .= " ORDER BY NOMBRE ASC, APELLIDOS ASC";

        $stmt = $conexion->prepare($sql);
        if (!empty($busqueda)) {
            $stmt->bindValue(':busqueda', '%' . $busqueda . '%');
        }
        $stmt->execute();
        $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger" role="alert">Error al cargar empleados: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    ?>


    <header>
        <a href="./inicio.php">
            <h6 style="color: #0c0e66;"><i class="bi bi-house-fill"></i> Volver a inicio</h6>
        </a>
    </header>

    <section class="d-flex justify-content-center">
        <h1 class="fs-3 mb-3">Lista de empleados</h1>
    </section>

    <article class="d-flex justify-content-center">
        <table>
            <thead>
                <tr>
                    <th class="pb-5"></th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Email</th>
                    <th>
                        <form method="GET" action="" class="d-flex justify-content-center">
                            <input type="text" name="busqueda" class="form-control w-50 text-center" placeholder="Buscar" value="<?php echo htmlspecialchars($busqueda); ?>">
                        </form>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($empleados) > 0): ?>
                    <?php foreach ($empleados as $empleado): ?>
                        <tr>
                            <td>
                                <?php if (!empty($empleado['FOTO'])): ?>
                                    <img src="<?php echo htmlspecialchars($empleado['FOTO']); ?>" alt="Foto de <?php echo htmlspecialchars($empleado['NOMBRE']); ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;" id="img_tabla">
                                <?php else: ?>
                                    <img src="./img/foto_default.svg" alt="Foto no disponible" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($empleado['NOMBRE']); ?></td>
                            <td><?php echo htmlspecialchars($empleado['APELLIDOS']); ?></td>
                            <td><?php echo htmlspecialchars($empleado['EMAIL']); ?></td>
                        </tr>
                        <hr>
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