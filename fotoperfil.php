<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['numero_empleado'])) {
    header('Location: index.php');
    exit();
}

$fotoPorDefecto = './img/foto_perfil/foto_default.svg';

$numero_empleado = isset($_GET['usuario']) ? $_GET['usuario'] : $_SESSION['numero_empleado'];

try {
    $sql = "SELECT username, foto FROM empleados WHERE numero_empleado = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$numero_empleado]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Error: No se encontraron los datos del usuario.");
    }

    $nombre_usuario = $user['username'];


    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        if (!empty($user['foto']) && $user['foto'] !== $fotoPorDefecto && file_exists($user['foto'])) {
            unlink($user['foto']);
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $nuevoNombre = 'foto_' . preg_replace('/\s+/', '_', $nombre_usuario) . '.' . $ext;

        $directorio = './img/foto_perfil/';
        $rutaDestino = $directorio . $nuevoNombre;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $rutaDestino)) {
            try {
                $sql = "UPDATE empleados SET foto = ? WHERE numero_empleado = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(1, $rutaDestino);
                $stmt->bindParam(2, $numero_empleado);
                $stmt->execute();

                header('Location: actualizar_perfil.php?usuario=' . $numero_empleado);
                exit();
            } catch (PDOException $e) {
                echo "Error al actualizar la foto: " . htmlspecialchars($e->getMessage());
            }
        } else {
            echo "Error al mover el archivo a la carpeta de fotos.";
        }
    } else {
        echo "No se seleccionó un archivo o hubo un error en la subida.";
    }
} catch (PDOException $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
}
?>