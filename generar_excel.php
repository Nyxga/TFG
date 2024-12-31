<?php
session_start();
include 'conexion.php';

require_once 'libs/SimpleXLSXGen.php';

use Shuchkin\SimpleXLSXGen;

$es_admin = isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true;

if (!empty($_POST['filtros_sql'])) {
    $fichajes = unserialize(base64_decode($_POST['filtros_sql']));
} else {
    $username = $_SESSION['username'] ?? null;

    if (!$es_admin && !$username) {
        die("Error: No se proporcionaron datos para generar el Excel.");
    }

    $sql = "SELECT l.fecha_hora, l.tipo_fichaje, l.dispositivo, l.ip, e.nombre, e.apellidos 
            FROM log_horarios l 
            JOIN empleados e ON l.numero_empleado = e.numero_empleado";
    $filtros = [];

    if (!$es_admin) {
        $filtros[] = "e.username = :username";
    }
    if (!empty($_POST['filtrar_fecha'])) {
        $filtros[] = "DATE(l.fecha_hora) = :filtrar_fecha";
    }
    if (!empty($_POST['filtrar_tipo'])) {
        $filtros[] = "l.tipo_fichaje = :filtrar_tipo";
    }

    if (!empty($filtros)) {
        $sql .= " WHERE " . implode(' AND ', $filtros);
    }

    $sql .= " ORDER BY l.fecha_hora DESC";

    $stmt = $conexion->prepare($sql);

    if (!$es_admin) {
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    }
    if (!empty($_POST['filtrar_fecha'])) {
        $stmt->bindParam(':filtrar_fecha', $_POST['filtrar_fecha'], PDO::PARAM_STR);
    }
    if (!empty($_POST['filtrar_tipo'])) {
        $stmt->bindParam(':filtrar_tipo', $_POST['filtrar_tipo'], PDO::PARAM_STR);
    }

    $stmt->execute();
    $fichajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($fichajes)) {
        die("Error: No se encontraron datos para generar el Excel.");
    }
}

$datos = [
    [
        '<center><b>Nombre</b></center>',
        '<center><b>Apellidos</b></center>',
        '<center><b>Fecha y Hora</b></center>',
        '<center><b>Tipo de Fichaje</b></center>',
        '<center><b>Dispositivo</b></center>',
        '<center><b>Dirección IP</b></center>'
    ],
];

foreach ($fichajes as $fichaje) {
    $datos[] = [
        '<center>' . $fichaje['nombre'] . '</center>',
        '<center>' . $fichaje['apellidos'] . '</center>',
        '<center>' . $fichaje['fecha_hora'] . '</center>',
        '<center>' . $fichaje['tipo_fichaje'] . '</center>',
        '<center>' . $fichaje['dispositivo'] . '</center>',
        '<center>' . $fichaje['ip'] . '</center>',
    ];
}

SimpleXLSXGen::fromArray($datos)->downloadAs('historial_fichajes.xlsx');
exit();