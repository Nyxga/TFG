<?php
session_start();
require 'conexion.php';

$foto_predeterminada = './img/foto_perfil/foto_default.svg';

if (isset($_SESSION['foto_actualizada'])) {
    $mensaje = $_SESSION['foto_actualizada'];
    unset($_SESSION['foto_actualizada']);
}

if (!isset($_SESSION['numero_empleado'])) {
    header('Location: ./index.php');
    exit();
}

$empleados = [];
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$numero_empleado = $_SESSION['numero_empleado'];

try {
    $sql = "select numero_empleado, nombre, apellidos, username, foto from empleados where numero_empleado != :numero_empleado";

    if (!empty($busqueda)) {
        $sql .= " and (nombre like :busqueda or apellidos like :busqueda or username like :busqueda)";
    }

    $sql .= " order by nombre asc, apellidos asc";

    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':numero_empleado', $numero_empleado, PDO::PARAM_INT);
    if (!empty($busqueda)) {
        $stmt->bindValue(':busqueda', '%' . $busqueda . '%');
    }
    $stmt->execute();
    $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<div class="alert alert-danger" role="alert">Error al cargar empleados: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

try {
    $sql = "select count(*) as total from empleados";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    $total_empleados = $resultado['total'];
} catch (Throwable $th) {
    echo '<div class="alert alert-danger" role="alert">No se han encontrado empleados.</div>';
}

// FOTOS EMPLEADOS
try {
    $numero_empleado = $_SESSION['numero_empleado'];
    $sql = "select numero_empleado, nombre, apellidos, foto, username from empleados where numero_empleado = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$numero_empleado]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $nombre = htmlspecialchars($user['nombre']);
        $apellidos = htmlspecialchars($user['apellidos']);
        $foto_url = htmlspecialchars($user['foto']);
        $username = htmlspecialchars($user['username']);
    }
} catch (PDOException $e) {
    echo '<div class="alert alert-danger" role="alert">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    session_start();

    try {
        $numero_empleado = $_SESSION['numero_empleado'];

        $sql = "select admin from empleados where numero_empleado = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$numero_empleado]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['admin'] == 1) {
            session_unset();
            session_destroy();
            header('Location: ../index.php');
            exit();
        }
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger" role="alert">Error al verificar el estado de administrador: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }

    session_unset();
    session_destroy();
    header('Location: ./index.php');
    exit();
}

function calcularHorasTrabajo($hora_inicio, $hora_fin)
{
    $inicio = new DateTime($hora_inicio);
    $fin = new DateTime($hora_fin);

    $diferencia = $inicio->diff($fin);

    return $diferencia->format('%H horas y %I minutos');
}
