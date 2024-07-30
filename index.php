<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>

    <main>
        <div id="login">
            <form action="index.php" method="POST">
                <picture>
                    <img src="./img/Logo.svg" alt="Logo" width="400px">
                </picture>
                <div class="mb-3">
                    <label for="empleado" class="form-label">Número de empleado</label>
                    <input type="number" id="empleado" name="empleado" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control" required>
                        <span class="input-group-text" id="verPassword">👀</span>
                    </div>
                </div>
                <div class="mb-3">
                    <a href="registro.php" style="color: grey;">Haz clic aquí si no tienes cuenta</a>
                </div>
                <button type="submit" class="btn btn-primary">Iniciar sesión</button>
            </form>
        </div>


        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            session_start();
            include 'conexion.php';

            $numero_empleado = $_POST['empleado'];
            $password = $_POST['password'];

            try {
                $sql = "SELECT PASSWORD FROM EMPLEADOS WHERE NUMERO_EMPLEADO = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([$numero_empleado]);

                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result && password_verify($password, $result['PASSWORD'])) {
                    $_SESSION['numero_empleado'] = $numero_empleado;
                    header('Location: inicio.php');
                    exit();
                } else {
                    echo '<div class="alert alert-danger" role="alert">Número de empleado o contraseña incorrectos.</div>';
                }
            } catch (PDOException $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }
        ?>



    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.getElementById('verPassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.textContent = type === 'password' ? '👀' : '🙈';
        });
    </script>
</body>

</html>