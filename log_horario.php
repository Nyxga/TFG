<?php
date_default_timezone_set('Europe/Madrid');
include 'listar_empleados.php';

if (!isset($_SESSION['numero_empleado'])) {
    header('Location: index.php');
    exit();
}

$numero_empleado = $_SESSION['numero_empleado'];

// Manejo del fichaje
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tipo'])) {
    $tipo_fichaje = $_POST['tipo'];
    $fecha_hora = date("Y-m-d H:i:s");
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $ip_cliente = $_SERVER['REMOTE_ADDR'];

    $os_pattern = '/(Windows|Macintosh|Android|iPhone|iPad|Ubuntu|Linux|Windows Phone|BlackBerry|FreeBSD|Debian|Chrome OS)/';
    preg_match($os_pattern, $user_agent, $os_matches);
    $os_name = isset($os_matches[1]) ? $os_matches[1] : 'Desconocido';

    try {
        $sql = "INSERT INTO log_horarios (NUMERO_EMPLEADO, TIPO_FICHAJE, FECHA_HORA, DISPOSITIVO, IP) VALUES (?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([$numero_empleado, $tipo_fichaje, $fecha_hora, $os_name, $ip_cliente]);

        header('Location: inicio.php');
        exit();
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger" role="alert">Error al fichar: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

// Manejo del filtro por fecha o reinicio
$fecha_filtrada = isset($_POST['filtrar_fecha']) ? $_POST['filtrar_fecha'] : null;
$reiniciar_filtro = isset($_POST['reiniciar_filtro']); // Verificar si se presionó el botón Reset

try {
    if ($reiniciar_filtro || !$fecha_filtrada) {
        // Si se presionó "Reset" o no hay fecha filtrada, cargar todos los registros
        $sql = "SELECT fecha_hora, tipo_fichaje, dispositivo 
                FROM log_horarios 
                WHERE numero_empleado = :numero_empleado 
                ORDER BY fecha_hora DESC";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':numero_empleado', $numero_empleado, PDO::PARAM_INT);
    } else {
        // Consulta filtrada por fecha
        $sql = "SELECT fecha_hora, tipo_fichaje, dispositivo 
                FROM log_horarios 
                WHERE numero_empleado = :numero_empleado AND DATE(fecha_hora) = :fecha_filtrada 
                ORDER BY fecha_hora DESC";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':numero_empleado', $numero_empleado, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_filtrada', $fecha_filtrada, PDO::PARAM_STR);
    }

    $stmt->execute();
    $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$empleados) {
        echo "No se encontraron registros de fichaje.";
    }
} catch (PDOException $e) {
    echo '<div class="alert alert-danger" role="alert">Error al cargar el historial: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
