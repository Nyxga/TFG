<?php
session_start();
require 'conexion.php';

$foto_predeterminada = './img/foto_default.svg';

if (isset($_SESSION['foto_actualizada'])) {
    $mensaje = $_SESSION['foto_actualizada'];
    unset($_SESSION['foto_actualizada']);
}

if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}


//LISTA DE EMPLEADOS
$empleados = [];
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

try {
    // Consulta SQL con filtro de búsqueda
    $sql = "SELECT NOMBRE, APELLIDOS, EMAIL, FOTO FROM EMPLEADOS";
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


//FOTOS EMPLEADOS
try {
    $email = $_SESSION['email'];
    $sql = "SELECT NOMBRE, APELLIDOS, FOTO FROM EMPLEADOS WHERE EMAIL = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$email]);
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
