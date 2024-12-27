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

// Manejo de los filtros por fecha y tipo
$fecha_filtrada = !empty($_POST['filtrar_fecha']) ? $_POST['filtrar_fecha'] : null;
$tipo_filtrado = !empty($_POST['filtrar_tipo']) ? $_POST['filtrar_tipo'] : null;

try {
    // Consulta para obtener todos los registros de la tabla log_horarios
    $sql_todos_registros = "SELECT * FROM log_horarios ORDER BY fecha_hora DESC";
    $stmt_todos_registros = $conexion->prepare($sql_todos_registros);
    $stmt_todos_registros->execute();
    $registros_todos = $stmt_todos_registros->fetchAll(PDO::FETCH_ASSOC);

    // Construir la consulta SQL dinámicamente para los filtros
    $sql_filtrada = "SELECT fecha_hora, tipo_fichaje, dispositivo 
                     FROM log_horarios 
                     WHERE numero_empleado = :numero_empleado";

    if ($fecha_filtrada) {
        $sql_filtrada .= " AND DATE(fecha_hora) = :fecha_filtrada";
    }
    if ($tipo_filtrado) {
        $sql_filtrada .= " AND tipo_fichaje = :tipo_filtrado";
    }
    $sql_filtrada .= " ORDER BY fecha_hora DESC";

    $stmt_filtrada = $conexion->prepare($sql_filtrada);
    $stmt_filtrada->bindParam(':numero_empleado', $numero_empleado, PDO::PARAM_INT);

    if ($fecha_filtrada) {
        $stmt_filtrada->bindParam(':fecha_filtrada', $fecha_filtrada, PDO::PARAM_STR);
    }
    if ($tipo_filtrado) {
        $stmt_filtrada->bindParam(':tipo_filtrado', $tipo_filtrado, PDO::PARAM_STR);
    }

    $stmt_filtrada->execute();
    $empleados_filtrados = $stmt_filtrada->fetchAll(PDO::FETCH_ASSOC);

    if (!$empleados_filtrados) {
        echo "No se encontraron registros con los filtros aplicados.";
    }
} catch (PDOException $e) {
    echo '<div class="alert alert-danger" role="alert">Error al cargar el historial: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
