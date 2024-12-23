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
$numero_empleado = $_SESSION['numero_empleado'];  // Suponiendo que tienes el número de empleado en la sesión

try {
    // Consulta SQL con filtro de búsqueda y exclusión del usuario logueado
    $sql = "SELECT NUMERO_EMPLEADO, NOMBRE, APELLIDOS, EMAIL, FOTO FROM EMPLEADOS WHERE NUMERO_EMPLEADO != :numero_empleado";
    
    // Si hay una búsqueda, añadimos los filtros correspondientes
    if (!empty($busqueda)) {
        $sql .= " AND (NOMBRE LIKE :busqueda OR APELLIDOS LIKE :busqueda OR EMAIL LIKE :busqueda)";
    }
    
    $sql .= " ORDER BY NOMBRE ASC, APELLIDOS ASC";

    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':numero_empleado', $numero_empleado, PDO::PARAM_INT);  // Excluir al usuario logueado
    if (!empty($busqueda)) {
        $stmt->bindValue(':busqueda', '%' . $busqueda . '%');
    }
    $stmt->execute();
    $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<div class="alert alert-danger" role="alert">Error al cargar empleados: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

//TOTAL EMPLEADOS
try {
    $sql = "SELECT COUNT(*) AS total FROM EMPLEADOS";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Acceder al total de empleados
    $total_empleados = $resultado['total'];
} catch (Throwable $th) {
    echo '<div class="alert alert-danger" role="alert">No se han encontrado empleados.</div>';
}



// FOTOS EMPLEADOS
try {
    $numero_empleado = $_SESSION['numero_empleado']; // Usamos el número de empleado desde la sesión
    $sql = "SELECT NUMERO_EMPLEADO, NOMBRE, APELLIDOS, FOTO, EMAIL FROM EMPLEADOS WHERE NUMERO_EMPLEADO = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$numero_empleado]); // Pasamos el número de empleado en la consulta
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $nombre = htmlspecialchars($user['NOMBRE']);
        $apellidos = htmlspecialchars($user['APELLIDOS']);
        $foto_url = htmlspecialchars($user['FOTO']);
        $email = htmlspecialchars($user['EMAIL']);
    }
} catch (PDOException $e) {
    echo '<div class="alert alert-danger" role="alert">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    session_start();

    try {
        $numero_empleado = $_SESSION['numero_empleado'];

        $sql = "SELECT ADMIN FROM EMPLEADOS WHERE NUMERO_EMPLEADO = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$numero_empleado]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['ADMIN'] == 1) {
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
    header('Location: index.php');
    exit();
}



function calcularHorasTrabajo($hora_inicio, $hora_fin) {
    $inicio = new DateTime($hora_inicio);
    $fin = new DateTime($hora_fin);

    $diferencia = $inicio->diff($fin);

    return $diferencia->format('%H horas y %I minutos');
}