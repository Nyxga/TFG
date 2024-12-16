<?php
include 'listar_empleados.php';

if (!isset($_SESSION['numero_empleado'])) {
    header('Location: index.php');
    exit();
}

$numero_empleado = $_SESSION['numero_empleado'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['tipo'])) {
        $tipo_fichaje = $_POST['tipo'];
        $fecha_hora = date("Y-m-d H:i:s");
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $ip_cliente = $_SERVER['REMOTE_ADDR'];

        // Detectar el sistema operativo
        $os_pattern = '/(Windows NT|Macintosh|Linux|Android|iPhone|iPad|Ubuntu|Windows Phone|BlackBerry|FreeBSD|Debian|Chrome OS)/';
        preg_match($os_pattern, $user_agent, $os_matches);
        $os_name = isset($os_matches[1]) ? $os_matches[1] : 'Desconocido';

        try {
            $sql = "INSERT INTO log_horarios (NUMERO_EMPLEADO, TIPO_FICHAJE, FECHA_HORA, DISPOSITIVO, IP) VALUES (?, ?, ?, ?, ?)";

            $stmt = $conexion->prepare($sql);
            $stmt->execute([$numero_empleado, $tipo_fichaje, $fecha_hora, $os_name, $ip_cliente]);

            // Mensaje de éxito
            echo "Registro insertado correctamente";
            header('Location: inicio.php');
            exit();
        } catch (PDOException $e) {
            echo '<div class="alert alert-danger" role="alert">Error al fichar' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}


// Realizar la consulta para obtener los registros de fichaje del empleado
$sql = "SELECT fecha_hora, tipo_fichaje, dispositivo FROM log_horarios WHERE numero_empleado = :numero_empleado ORDER BY fecha_hora DESC";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(':numero_empleado', $numero_empleado, PDO::PARAM_INT);
$stmt->execute();

// Obtener los resultados
$empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar si hay resultados
if (!$empleados) {
    echo "No se encontraron registros de fichaje.";
}

$conexion = null;
