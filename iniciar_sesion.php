<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        session_start();
        require 'conexion.php';

        $email = $_POST['email'];
        $password = $_POST['password'];

        try {
            $sql = "SELECT NUMERO_EMPLEADO, PASSWORD FROM EMPLEADOS WHERE EMAIL = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$email]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && password_verify($password, $result['PASSWORD'])) {
                $_SESSION['numero_empleado'] = $result['NUMERO_EMPLEADO'];
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