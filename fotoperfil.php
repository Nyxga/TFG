<?php
session_start();
include 'conexion.php';

// Verifica si el usuario inició sesión
if (!isset($_SESSION['numero_empleado'])) {
    header('Location: index.php');
    exit();
}

// Obtén el número de empleado de la URL o usa el de la sesión como predeterminado
$numero_empleado = isset($_GET['usuario']) ? $_GET['usuario'] : $_SESSION['numero_empleado'];

try {
    // Obtiene los datos del empleado
    $sql = "select nombre, apellidos, foto from empleados where numero_empleado = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$numero_empleado]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Error: No se encontraron los datos del usuario.");
    }

    $nombre = $user['nombre'];
    $apellidos = $user['apellidos'];

    // Verifica si se subió un archivo
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Elimina la foto anterior si existe
        if (!empty($user['foto']) && file_exists($user['foto'])) {
            unlink($user['foto']);
        }

        // Genera un nuevo nombre para la foto
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $nuevoNombre = 'foto_' . preg_replace('/\s+/', '_', $nombre . '_' . $apellidos) . '.' . $ext;

        $directorio = './img/foto_perfil/';
        $rutaDestino = $directorio . $nuevoNombre;

        // Mueve el archivo a la carpeta destino
        if (move_uploaded_file($_FILES['image']['tmp_name'], $rutaDestino)) {
            try {
                $sql = "update empleados set foto = ? where numero_empleado = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(1, $rutaDestino);
                $stmt->bindParam(2, $numero_empleado);
                $stmt->execute();

                // Redirige a actualizar_perfil.php con el parámetro usuario
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
