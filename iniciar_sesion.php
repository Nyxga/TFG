<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        session_start();
        require 'conexion.php';

        $email = $_POST['email'];
        $password = $_POST['password'];

        try {
            $sql = "SELECT NUMERO_EMPLEADO, PASSWORD, ADMIN FROM EMPLEADOS WHERE EMAIL = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$email]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['PASSWORD'])) {
                if ($user['ADMIN'] == 1) {
                    $_SESSION['numero_empleado'] = $user['NUMERO_EMPLEADO'];
                    $_SESSION['admin'] = true;
                    header('Location: ./admin/inicio.php');
                    exit();
                } else {
                    $_SESSION['numero_empleado'] = $user['NUMERO_EMPLEADO'];
                    header('Location: inicio.php');
                    exit();
                }
            } else {
                echo '<div class="alert alert-danger text-center alerta-fija" role="alert">Dirección de correo o contraseña incorrectos.</div>';
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
    ?>