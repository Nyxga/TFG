<?php
session_start();
require 'conexion.php';

$foto_predeterminada = './img/foto_default.svg';

if (isset($_SESSION['foto_actualizada'])) {
    $mensaje = $_SESSION['foto_actualizada'];
    unset($_SESSION['foto_actualizada']);
}

// Asegurarnos de que el número de empleado esté en la sesión
if (!isset($_SESSION['numero_empleado'])) {
    header('Location: index.php');
    exit();
}

// LISTA DE EMPLEADOS
$empleados = [];
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

try {
    // Consulta SQL con filtro de búsqueda
    $sql = "SELECT NUMERO_EMPLEADO, NOMBRE, APELLIDOS, EMAIL, FOTO FROM EMPLEADOS";
    if (!empty($busqueda)) {
        $sql .= " WHERE NOMBRE LIKE :busqueda OR APELLIDOS LIKE :busqueda OR EMAIL LIKE :busqueda";
    }
    $sql .= " ORDER BY NOMBRE ASC, APELLIDOS ASC";

    $stmt = $conexion->prepare($sql);
    if (!empty($busqueda)) {
        $stmt->bindValue(':busqueda', '%' . $busqueda . '%');
    }
    $stmt->execute();
    $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<div class="alert alert-danger" role="alert">Error al cargar empleados: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

// FOTOS EMPLEADOS
try {
    $numero_empleado = $_SESSION['numero_empleado']; // Usamos el número de empleado desde la sesión
    $sql = "SELECT NUMERO_EMPLEADO, NOMBRE, APELLIDOS, FOTO FROM EMPLEADOS WHERE NUMERO_EMPLEADO = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$numero_empleado]); // Pasamos el número de empleado en la consulta
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $nombre = htmlspecialchars($user['NOMBRE']);
        $apellidos = htmlspecialchars($user['APELLIDOS']);
        $foto_url = htmlspecialchars($user['FOTO']);
    }
} catch (PDOException $e) {
    echo '<div class="alert alert-danger" role="alert">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    session_unset();
    session_destroy();

    header('Location: index.php');
    exit();
}
?>
