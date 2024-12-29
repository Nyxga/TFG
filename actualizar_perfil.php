<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre mi</title>
    <link rel="stylesheet" href="./css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body id="actualizar_perfil">
    <?php
    include 'listar_empleados.php';

    if (!isset($_SESSION['numero_empleado'])) {
        die('Acceso denegado. Por favor, inicie sesión.');
    }

    $rol_sesion = $_SESSION['admin'];

    if (isset($_GET['usuario'])) {
        $numero_empleado = $_GET['usuario'];

        if ($numero_empleado != $_SESSION['numero_empleado'] && $rol_sesion != 'admin') {
            die('No tienes permisos para modificar la contraseña de otro empleado.');
        }

        try {
            $sql = "SELECT * FROM empleados WHERE numero_empleado = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$numero_empleado]);
            $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$empleado) {
                die('Empleado no encontrado.');
            }
        } catch (PDOException $e) {
            die('Error al consultar los datos del empleado: ' . $e->getMessage());
        }
    } else {
        die('No se especificó un empleado.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password === $confirm_password) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            try {
                $sql = "UPDATE empleados SET password = ? WHERE numero_empleado = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([$hashed_password, $numero_empleado]);

                $_SESSION['successMensaje'] = 'Contraseña actualizada correctamente.';
                header('Location: actualizar_perfil.php?usuario=' . $numero_empleado);
                exit;
            } catch (PDOException $e) {
                $_SESSION['errorMensaje'] = 'Error al actualizar la contraseña.';
                header('Location: actualizar_perfil.php?usuario=' . $numero_empleado);
                exit;
            }
        } else {
            $_SESSION['warningMensaje'] = 'Las contraseñas no coinciden.';
            header('Location: actualizar_perfil.php?usuario=' . $numero_empleado);
            exit;
        }
    }
    ?>

    <header class="p-4 mb-0">
        <a href="<?php echo $rol_sesion ? './admin/inicio.php' : './inicio.php'; ?>">
            <h6 style="color: #0c0e66;"><i class="bi bi-house-fill"></i> Volver a inicio</h6>
        </a>
    </header>


    <main class="h-100 p-4">
        <section class="d-flex">
            <h1 class="fs-3">Actualizar el perfil</h1>
        </section>
        <hr>
        <?php
        if (isset($_SESSION['successMensaje'])) {
            echo '<div class="alert alert-success text-center alerta-fija" role="alert">';
            echo $_SESSION['successMensaje'];
            echo '</div>';
            unset($_SESSION['successMensaje']);
        }

        if (isset($_SESSION['warningMensaje'])) {
            echo '<div class="alert alert-warning text-center alerta-fija" role="alert">';
            echo $_SESSION['warningMensaje'];
            echo '</div>';
            unset($_SESSION['warningMensaje']);
        }

        if (isset($_SESSION['errorMensaje'])) {
            echo '<div class="alert alert-danger text-center alerta-fija" role="alert">';
            echo $_SESSION['errorMensaje'];
            echo '</div>';
            unset($_SESSION['errorMensaje']);
        }
        ?>
        <article class="d-flex">
            <table>
                <tr>
                    <td>
                        <form action="?usuario=<?php echo $numero_empleado; ?>" method="POST">
                            <div id="contenedor_info_perfil">
                                <label>Nombre</label>
                                <input type="text" class="form-control mb-4 w-auto" readonly disabled value="<?php echo htmlspecialchars($empleado['NOMBRE'] ?? ''); ?>">

                                <label>Apellidos</label>
                                <input type="text" class="form-control mb-4 w-auto" readonly disabled value="<?php echo htmlspecialchars($empleado['APELLIDOS'] ?? ''); ?>">

                                <label>Nombre de usuario</label>
                                <input type="text" class="form-control mb-4 w-auto" readonly disabled value="<?php echo htmlspecialchars($empleado['USERNAME'] ?? ''); ?>">

                                <label for="password">Nueva contraseña</label>
                                <div class="input-group mb-4 w-auto">
                                    <input type="password" id="password" name="password" class="form-control" required minlength="8">
                                    <span class="input-group-text">
                                        <i class="bi bi-eye-slash toggle-password" data-target="password"></i>
                                    </span>
                                </div>

                                <label for="confirm_password">Confirmar nueva contraseña</label>
                                <div class="input-group mb-4 w-auto">
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="8">
                                    <span class="input-group-text">
                                        <i class="bi bi-eye-slash toggle-password" data-target="confirm_password"></i>
                                    </span>
                                </div>

                                <button type="submit" class="btn btn-success">Confirmar cambios</button>
                            </div>
                        </form>
                    </td>
                    <td class="d-flex">
                        <div id="contenedor_actualizar_foto" class="d-flex flex-column mx-4">

                            <button type="button" id="cambiarFoto" class="dropbtn mt-2">
                                <img src="<?php echo htmlspecialchars($empleado['FOTO']); ?>" alt="Foto de perfil">
                            </button>

                            <form action="fotoperfil.php?usuario=<?php echo $numero_empleado; ?>" method="post" enctype="multipart/form-data" style="display: none;" id="form-cambiar-foto">
                                <input type="file" name="image" accept="image/*" id="input-foto" onchange="document.getElementById('form-cambiar-foto').submit();">
                            </form>

                        </div>
                    </td>
                </tr>
            </table>
        </article>

        <!-- <section class="d-flex position-absolute bottom-0 start-0 p-1">
            <a href="./inicio.php" class="text-dark"><i class="bi bi-arrow-bar-left"></i> Volver a inicio</a>
        </section> -->
    </main>


    <script>
        document.getElementById('cambiarFoto').addEventListener('click', function() {
            document.getElementById('input-foto').click();
        });
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