<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de empleado</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body id="registro">
    <?php
    include '../listar_empleados.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = trim($_POST['nombre']);
        $apellidos = trim($_POST['apellidos']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            $_SESSION['warningMensaje'] = 'Las contraseñas no coinciden.';
        } else {
            $username_base = strtolower(preg_replace('/[^a-z]/i', '', $apellidos)) . strtolower($nombre[0]);
            $username = $username_base;
            $sufijo = 1;

            while (true) {
                $stmt = $conexion->prepare("SELECT COUNT(*) FROM empleados WHERE username = ?");
                $stmt->execute([$username]);
                if ($stmt->fetchColumn() == 0) {
                    break;
                }
                $username = $username_base . $sufijo;
                $sufijo++;
            }

            try {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conexion->prepare("INSERT INTO empleados (nombre, apellidos, username, password, foto) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $apellidos, $username, $hashed_password, $foto_predeterminada]);
                $_SESSION['mensaje'] = 'Empleado registrado con éxito. Nombre de usuario: <strong>' . htmlspecialchars($username) . '</strong>';
            } catch (PDOException $e) {
                $_SESSION['mensaje'] = 'Error al registrar el empleado: ' . $e->getMessage();
            }
        }
    }
    ?>

    <header class="p-4 mb-0">
        <a href="./inicio.php">
            <h6 class="text-dark"><i class="bi bi-house-fill text-dark"></i> Volver a inicio</h6>
        </a>
    </header>


    <main class="h-100 p-4">
        <section class="d-flex">
            <h1 class="fs-3">Registrar empleado</h1>
        </section>

        <hr>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success text-center">
                <?php echo $_SESSION['mensaje']; ?>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>


        <article class="d-flex">
            <table>
                <tr>
                    <td>
                        <form action="?usuario=<?php echo $numero_empleado; ?>" method="POST">
                            <div id="contenedor_info_perfil">
                                <label>Nombre</label>
                                <input type="text" id="nombre" name="nombre" class="form-control mb-4 w-auto">

                                <label>Apellidos</label>
                                <input type="text" id="apellidos" name="apellidos" class="form-control mb-4 w-auto">

                                <label for="password">Contraseña</label>
                                <div class="input-group mb-4 w-auto">
                                    <input type="password" id="password" name="password" class="form-control" required minlength="8" placeholder="Min. 8 carácteres">
                                    <span class="input-group-text">
                                        <i class="bi bi-eye-slash toggle-password" data-target="password"></i>
                                    </span>
                                </div>

                                <label for="confirm_password">Confirmar contraseña</label>
                                <div class="input-group mb-4 w-auto">
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="8" placeholder="Min. 8 carácteres">
                                    <span class="input-group-text">
                                        <i class="bi bi-eye-slash toggle-password" data-target="confirm_password"></i>
                                    </span>
                                </div>

                                <button type="submit" class="btn btn-success"><i class="bi bi-check2"></i> Confirmar cambios</button>
                                <button type="button" class="btn btn-danger mx-2" onclick="window.location.href='./registro.php'">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
            </table>
        </article>

    </main>


    <script>
        function previewFoto(event) {
            const fotoPreview = document.getElementById('foto_registro');
            fotoPreview.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>

    <script>
        document.querySelectorAll('.toggle-password').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);

                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>