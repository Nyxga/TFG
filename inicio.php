<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="./css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        @font-face {
            font-family: 'TAN-HEADLINE';
            src: url('./fuentes/TAN-HEADLINE.woff2') format('woff2'), url('./fuentes/TAN-HEADLINE.ttf') format('truetype');
        }
    </style>
</head>

<body>
    <main>
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

        <aside style="background-color: white;">
            <h1 id="letras_Orion" style="font-size: 40px; margin-top: 10px;">Orion</h1>
            <nav>
                <ul>
                    <li><a href="#">Empleados</a></li>
                    <li><a href="#">Registros</a></li>
                    <li><a href="#">Permisos</a></li>
                    <li><a href="#">Turnos</a></li>
                    <li><a href="#">Nóminas</a></li>
                    <li><a href="#">Documentos</a></li>
                    <!-- Añade más enlaces según sea necesario -->
                </ul>
            </nav>
        </aside>


    </main>





    <!-- NO TOCAR -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/darkmode-js@1.5.7/lib/darkmode-js.min.js"></script>
    <script>
        function addDarkmodeWidget() {
            new Darkmode().showWidget();
        }
        window.addEventListener('load', addDarkmodeWidget);
    </script>
</body>

</html>