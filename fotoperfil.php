<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

$email = $_SESSION['email'];

$sql = "SELECT NOMBRE, APELLIDOS, FOTO FROM EMPLEADOS WHERE EMAIL = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $nombre = $user['NOMBRE'];
    $apellidos = $user['APELLIDOS'];

    if (!empty($user['FOTO'])) {
        $fotoAnterior = $user['FOTO'];
        if (file_exists($fotoAnterior)) {
            unlink($fotoAnterior);
        }
    }


    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

    $nuevoNombre = 'foto_' . $nombre . ' ' . $apellidos . '.' . $ext;

    $directorio = './img/foto_perfil/';

    $rutaDestino = $directorio . $nuevoNombre;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $rutaDestino)) {
        try {
            $sql = "UPDATE EMPLEADOS SET FOTO = ? WHERE EMAIL = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(1, $rutaDestino);
            $stmt->bindParam(2, $email);
            $stmt->execute();

            header('Location: inicio.php');
            exit();
        } catch (PDOException $e) {
            echo "Error al actualizar la foto: " . $e->getMessage();
        }
    } else {
        echo "Error al mover el archivo a la carpeta de fotos.";
    }
} else {
    echo "Error: No se encontraron los datos del usuario.";
}
?>
