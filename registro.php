<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de empleado</title>
    <link rel="stylesheet" href="./css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <style>
            .alerta-fija {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            width: 50%;
            }

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

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $foto_default = './img/Usuario.svg';

            if (strpos($email, '@orion.net') === false) {
                $_SESSION['errorMensaje'] = 'El correo electrónico debe tener el dominio <strong>@orion.net</strong>';
            } else {
                try {
                    $sql = "INSERT INTO EMPLEADOS (NOMBRE, APELLIDO, EMAIL, PASSWORD, FOTO) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conexion->prepare($sql);
                    $stmt->execute([$nombre, $apellido, $email, $password, $foto_default]);
                    $_SESSION['successMensaje'] = 'El usuario <strong>' . $nombre . '</strong> 
                    ha sido registrado con éxito.<br>Redirigiendo a la página de inicio...';
                    echo '<div class="alert alert-success text-center alerta-fija" role="alert">';
                    echo $_SESSION['successMensaje'];
                    echo '</div>';
                    echo '<script>
                            setTimeout(function(){
                                window.location.href = "index.php";
                            }, 3000);
                          </script>';
                } catch (PDOException $e) {
                    echo 'Error: ' . $e->getMessage();
                }
            }
        }

        if (isset($_SESSION['errorMensaje'])) {
            echo '<div class="alert alert-danger text-center alerta-fija" role="alert">';
            echo $_SESSION['errorMensaje'];
            echo '</div>';
            unset($_SESSION['errorMensaje']);
        }
        ?>

        <div id="registro">
            <form action="registro.php" method="POST">
                <h1 id="letras_Orion" style="margin-bottom: 20px;">Orion</h1>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="apellido" class="form-label">Apellido</label>
                    <input type="text" id="apellido" name="apellido" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Dirección de correo</label>
                    <input type="text" id="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="confirmar_password" class="form-label">Confirmar Contraseña</label>
                    <input type="password" id="confirmar_password" name="confirmar_password" class="form-control" required>
                    <div id="mensaje-error" class="text-danger mt-2" style="display: none;">
                        Las contraseñas no coinciden.
                    </div>
                </div>
                <button type="submit" class="btn btn-dark darkmode-ignore">Registrar</button>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        document.querySelector('form').addEventListener('submit', function (event) {
            var password = document.getElementById('password').value;
            var confirmar_password = document.getElementById('confirmar_password').value;
            if (password !== confirmar_password) {
                event.preventDefault();
                document.getElementById('mensaje-error').style.display = 'block';
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/darkmode-js@1.5.7/lib/darkmode-js.min.js"></script>
    <script>
        function addDarkmodeWidget() {
            new Darkmode().showWidget();
        }
        window.addEventListener('load', addDarkmodeWidget);
    </script>
</body>

</html>
