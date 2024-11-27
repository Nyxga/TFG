<?php
// Obtener el número de empleado del remitente (usuario logueado)
$usuario_emisor = $_SESSION['numero_empleado'];
$usuario_receptor = $_GET['usuario'];

// Obtener el número de empleado del receptor (usuario con el que se está chateando)

// Procesar el mensaje enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mensaje'])) {
    $mensaje = trim($_POST['mensaje']);

    // Verificar que el mensaje no esté vacío
    if (!empty($mensaje)) {
        // Insertar el mensaje en la base de datos
        $sql = "INSERT INTO mensajes (emisor, receptor, mensaje) VALUES (:emisor, :receptor, :mensaje)";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':emisor', $usuario_emisor);
        $stmt->bindParam(':receptor', $usuario_receptor);
        $stmt->bindParam(':mensaje', $mensaje);
        
        if ($stmt->execute()) {
            // Redirigir para evitar reenvío del formulario en caso de refresco
            header('Location: chat.php?usuario=' . $usuario_receptor);
            exit();
        } else {
            echo 'Error al enviar el mensaje.';
        }
    }
}

// Obtener los mensajes entre el emisor y el receptor
$sql_mensajes = "SELECT * FROM mensajes WHERE (emisor = :emisor AND receptor = :receptor) OR (emisor = :receptor AND receptor = :emisor) ORDER BY id ASC";
$stmt_mensajes = $conexion->prepare($sql_mensajes);
$stmt_mensajes->bindParam(':emisor', $usuario_emisor);
$stmt_mensajes->bindParam(':receptor', $usuario_receptor);
$stmt_mensajes->execute();

$mensajes = $stmt_mensajes->fetchAll(PDO::FETCH_ASSOC);

?>