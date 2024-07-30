<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="./css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
</head>

<body>
    <?php
    session_start();
    include 'conexion.php';

    if (!isset($_SESSION['numero_empleado'])) {
        header('Location: index.php');
        exit();
    }

    try {
        $numero_empleado = $_SESSION['numero_empleado'];
        $sql = "SELECT NOMBRE, APELLIDO, FOTO FROM EMPLEADOS WHERE NUMERO_EMPLEADO = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$numero_empleado]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $nombre = htmlspecialchars($user['NOMBRE']);
            $apellido = htmlspecialchars($user['APELLIDO']);
            $foto_url = htmlspecialchars($user['FOTO']);
        }
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger" role="alert">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
        session_unset();
        session_destroy();

        header('Location: index.php');
        exit();
    }
    ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="./img/Logo.svg" alt="Logo Orion">
        </a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link me-3" href="#">
                        <i class="bi bi-chat"></i> Chat
                    </a>
                </li>
                <li class="nav-item d-flex align-items-center me-3">
                    <img src="<?php echo $foto_url; ?>" alt="Foto del usuario" class="img-fluid rounded-circle me-2">
                    <span><?php echo $nombre . ' ' . $apellido; ?></span>
                </li>
            </ul>
        </div>
    </nav>
    <aside class="sidebar">
        <div class="d-flex align-items-center mb-4">
            <picture>
                <img src="<?php echo $foto_url; ?>" alt="Foto del usuario" class="img-fluid rounded-circle" style="width: 60px;">
            </picture>
            <div class="ms-3 mt-2">
                <h6><?php echo $nombre; ?></h6>
                <h6><?php echo $apellido; ?></h6>
            </div>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link active" href="#">Inicio</a>
            <a class="nav-link" href="#">Perfil</a>
            <a class="nav-link" href="#">Configuración</a>
            <a class="nav-link" href="#">Reportes</a>
        </nav>
    </aside>
    <main class="main-content">

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>