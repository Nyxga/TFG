<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
            .alerta-fija {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            width: 50%;
            }
        </style>
</head>

<body>

    <main>
        <div id="login">
            <form action="index.php" method="POST">
                <h1 id="letras_Orion" class="text-center">Orion</h1>
                <div class="mb-3">
                    <input type="email" id="email" name="email" class="form-control" placeholder="Dirección de correo" required>
                </div>
                <div class="mb-3">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Contraseña" required>
                </div>
                <div class="mb-3">
                    <a href="./registro.php" style="color: grey;">Haz clic aquí si no tienes cuenta</a>
                </div>
                <button type="submit" class="btn btn-dark mt-20">Iniciar sesión</button>
            </form>
        </div>


        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            session_start();
            include 'conexion.php';

            $email = $_POST['email'];
            $password = $_POST['password'];

            try {
                $sql = "SELECT PASSWORD FROM EMPLEADOS WHERE EMAIL = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([$email]);

                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result && password_verify($password, $result['PASSWORD'])) {
                    $_SESSION['email'] = $email;
                    header('Location: inicio.php');
                    exit();
                } else {
                    echo '<div class="alert alert-danger text-center alerta-fija" role="alert">Dirección de correo o contraseña incorrectos.</div>';
                }
            } catch (PDOException $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }
        ?>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>