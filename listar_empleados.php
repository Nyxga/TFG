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


// HORARIOS EMPLEADOS
$query = "SELECT dia_semana, hora_inicio, hora_fin FROM horarios WHERE NUMERO_EMPLEADO = :numero_empleado";
$stmt = $conexion->prepare($query);
$stmt->bindParam(':numero_empleado', $numero_empleado, PDO::PARAM_INT);
$stmt->execute();

$dias_semana = [
    'Lunes' => null,
    'Martes' => null,
    'Miércoles' => null,
    'Jueves' => null,
    'Viernes' => null,
    'Sábado' => null,
    'Domingo' => null,
];

// Rellenar el array con datos de la consulta
while ($horario = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $dias_semana[$horario['dia_semana']] = [
        'hora_inicio' => $horario['hora_inicio'],
        'hora_fin' => $horario['hora_fin'],
    ];
    
}

// var_dump($dias_semana);
// exit();

function calcularHorasTrabajo($hora_inicio, $hora_fin) {
    $inicio = new DateTime($hora_inicio);
    $fin = new DateTime($hora_fin);

    $diferencia = $inicio->diff($fin);

    return $diferencia->format('%H horas y %I minutos');
}