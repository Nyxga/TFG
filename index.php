<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>

    <main>
        <div id="login">
            <form action="index.php" method="POST">
                <picture>
                    <img src="/img/Logo.svg" alt="Logo" width="400px">
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
        session_start();

        // Verificar si se han enviado datos por POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include('conexion.php'); // Incluir archivo de conexión a la base de datos
            
            $empleado = $_POST['empleado'];
            $password = $_POST['password'];

            // Consulta para buscar empleado por número de empleado
            $consulta = $connection->prepare("SELECT * FROM empleados WHERE numero_empleado = :empleado");
            $consulta->bindParam(":empleado", $empleado, PDO::PARAM_INT);
            $consulta->execute();

            // Obtener el resultado de la consulta
            $empleado = $consulta->fetch(PDO::FETCH_ASSOC);

            // Verificar si se encontró un empleado y verificar la contraseña
            if ($empleado && password_verify($password, $empleado['password'])) {
                // Iniciar sesión
                $_SESSION['empleado_id'] = $empleado['id'];
                $_SESSION['empleado_nombre'] = $empleado['nombre'];

                // Redirigir a la página de inicio
                header("Location: inicio.php");
                exit;
            } else {
                echo '<div class="alert alert-danger" role="alert">Número de empleado o contraseña incorrectos.</div>';
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
