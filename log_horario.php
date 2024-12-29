<?php
// Configurar zona horaria y conexión
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
    $fecha_actual = date("Y-m-d");
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $ip_cliente = $_SERVER['REMOTE_ADDR'];

    $os_pattern = '/(Windows|Macintosh|Android|iPhone|iPad|Ubuntu|Linux|Windows Phone|BlackBerry|FreeBSD|Debian|Chrome OS)/';
    preg_match($os_pattern, $user_agent, $os_matches);
    $os_name = isset($os_matches[1]) ? $os_matches[1] : 'Desconocido';

    try {
        // Comprobar si ya existe un fichaje del mismo tipo en el día actual
        $sql = "select count(*) as total from log_horarios 
                where numero_empleado = ? and tipo_fichaje = ? and date(fecha_hora) = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$numero_empleado, $tipo_fichaje, $fecha_actual]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['total'] > 0) {
            $_SESSION['error_message'] = "Ya has registrado un fichaje de tipo <strong>$tipo_fichaje</strong>";
            header('Location: inicio.php');
            exit();
        }

        // Insertar el fichaje si no hay problemas
        $sql = "insert into log_horarios (numero_empleado, tipo_fichaje, fecha_hora, dispositivo, ip) values (?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$numero_empleado, $tipo_fichaje, $fecha_hora, $os_name, $ip_cliente]);

        $_SESSION['success_message'] = "Fichaje de tipo <strong>$tipo_fichaje</strong> registrado correctamente.";
        header('Location: inicio.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'Error al fichar: ' . htmlspecialchars($e->getMessage());
        header('Location: inicio.php');
        exit();
    }
}

// Manejo del filtro por fecha y tipo o reinicio
$fecha_filtrada = isset($_POST['filtrar_fecha']) ? $_POST['filtrar_fecha'] : null;
$tipo_filtrado = isset($_POST['filtrar_tipo']) ? $_POST['filtrar_tipo'] : null;
$reiniciar_filtro = isset($_POST['reiniciar_filtro']);

try {
    if ($reiniciar_filtro || (!$fecha_filtrada && !$tipo_filtrado)) {
        // Si se presionó "Reset" o no hay filtros, cargar todos los registros
        $sql = "select fecha_hora, tipo_fichaje, dispositivo 
                from log_horarios 
                where numero_empleado = :numero_empleado 
                order by fecha_hora desc";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':numero_empleado', $numero_empleado, PDO::PARAM_INT);
    } elseif ($fecha_filtrada && !$tipo_filtrado) {
        // Filtrar solo por fecha
        $sql = "select fecha_hora, tipo_fichaje, dispositivo 
                from log_horarios 
                where numero_empleado = :numero_empleado and date(fecha_hora) = :fecha_filtrada 
                order by fecha_hora desc";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':numero_empleado', $numero_empleado, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_filtrada', $fecha_filtrada, PDO::PARAM_STR);
    } elseif (!$fecha_filtrada && $tipo_filtrado) {
        // Filtrar solo por tipo
        $sql = "select fecha_hora, tipo_fichaje, dispositivo 
                from log_horarios 
                where numero_empleado = :numero_empleado and tipo_fichaje = :tipo_filtrado 
                order by fecha_hora desc";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':numero_empleado', $numero_empleado, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_filtrado', $tipo_filtrado, PDO::PARAM_STR);
    } else {
        // Filtrar por fecha y tipo
        $sql = "select fecha_hora, tipo_fichaje, dispositivo 
                from log_horarios 
                where numero_empleado = :numero_empleado and date(fecha_hora) = :fecha_filtrada and tipo_fichaje = :tipo_filtrado 
                order by fecha_hora desc";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':numero_empleado', $numero_empleado, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_filtrada', $fecha_filtrada, PDO::PARAM_STR);
        $stmt->bindParam(':tipo_filtrado', $tipo_filtrado, PDO::PARAM_STR);
    }

    $stmt->execute();
    $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<div class="alert alert-danger" role="alert">Error al cargar el historial: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
