<?php
// Obtener el número de empleado del remitente (usuario logueado)
$usuario_emisor = $_SESSION['numero_empleado'];

// Verificar si se ha seleccionado un usuario para chatear
if (isset($_GET['usuario']) && !empty($_GET['usuario'])) {
    $usuario_receptor = $_GET['usuario'];
    
    // Obtener la foto del receptor desde la base de datos
    $sql_foto_receptor = "SELECT FOTO FROM empleados WHERE NUMERO_EMPLEADO = :numero_empleado";
    $stmt_foto_receptor = $conexion->prepare($sql_foto_receptor);
    $stmt_foto_receptor->bindParam(':numero_empleado', $usuario_receptor, PDO::PARAM_INT);
    $stmt_foto_receptor->execute();
    
    $foto_receptor = $stmt_foto_receptor->fetch(PDO::FETCH_ASSOC);
    $foto_seleccionada = $foto_receptor && !empty($foto_receptor['FOTO']) ? $foto_receptor['FOTO'] : './img/foto_default.svg';
} else {
    // En caso de que no se haya seleccionado un usuario, manejarlo según sea necesario
    $usuario_receptor = null;
    $foto_seleccionada = './img/foto_default.svg';
}

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

// Obtener los datos del receptor
$sql_receptor = "SELECT NOMBRE, APELLIDOS FROM empleados WHERE NUMERO_EMPLEADO = :receptor";
$stmt_receptor = $conexion->prepare($sql_receptor);
$stmt_receptor->bindParam(':receptor', $usuario_receptor);
$stmt_receptor->execute();
$receptor = $stmt_receptor->fetch(PDO::FETCH_ASSOC);

// Verificar si se encontró el receptor
if ($receptor) {
    $nombre_receptor = $receptor['NOMBRE'];
    $apellidos_receptor = $receptor['APELLIDOS'];
} else {
    $nombre_receptor = "Desconocido";
    $apellidos_receptor = "";
}


