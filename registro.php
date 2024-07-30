<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de empleado</title>
    <link rel="stylesheet" href="./css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <main>
        <div id="registro">
            <form action="registro.php" method="POST">
                <picture>
                    <img src="./img/Logo.svg" alt="Logo" width="400px">
                </picture>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="apellido" class="form-label">Apellido</label>
                    <input type="text" id="apellido" name="apellido" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="text" id="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="confirmar_password" class="form-label">Confirmar Contraseña</label>
                    <input type="password" id="confirmar_password" name="confirmar_password" class="form-control"
                        required>
                    <div id="mensaje-error" class="text-danger mt-2" style="display: none;">
                        Las contraseñas no coinciden.
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Registrar</button>
            </form>
        </div>


        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            session_start();
            include 'conexion.php';

            $numero_empleado = rand(0, 100000);
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $foto_default = './img/Usuario.svg';

            try {
                $sql = "INSERT INTO EMPLEADOS (NUMERO_EMPLEADO, NOMBRE, APELLIDO, EMAIL, PASSWORD, FOTO) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([$numero_empleado, $nombre, $apellido, $email, $password, $foto_default]);
                echo 'Registro exitoso';
            } catch (PDOException $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }
        ?>


    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        // Validar que las contraseñas coincidan antes de enviar el formulario
        document.querySelector('form').addEventListener('submit', function (event) {
            var password = document.getElementById('password').value;
            var confirmar_password = document.getElementById('confirmar_password').value;
            if (password !== confirmar_password) {
                event.preventDefault(); // Prevenir el envío del formulario
                document.getElementById('mensaje-error').style.display = 'block'; // Mostrar mensaje de error
            }
        });
    </script>
</body>

</html>
