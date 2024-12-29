<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start();
    require 'conexion.php';

    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $sql = "select numero_empleado, password, admin from empleados where username = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$username]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if ($user['admin'] == 1) {
                $_SESSION['numero_empleado'] = $user['numero_empleado'];
                $_SESSION['admin'] = true;
                header('Location: ./admin/inicio.php');
                exit();
            } else {
                $_SESSION['numero_empleado'] = $user['numero_empleado'];
                $_SESSION['admin'] = false;
                header('Location: ./inicio.php');
                exit();
            }
        } else {
            echo '<div class="alert alert-danger text-center alerta-fija" role="alert">Nombre de usuario o contraseña incorrectos.</div>';
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
?>
