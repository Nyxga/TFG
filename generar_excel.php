<?php
require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Iniciar sesión para verificar permisos
session_start();
include 'conexion.php'; // Incluye la conexión a la base de datos

// Verificar si el usuario es administrador
$es_admin = isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true;

// Verificar si se proporcionaron datos desde historial_fichajes.php
if (!empty($_POST['filtros_sql'])) {
    $fichajes = unserialize(base64_decode($_POST['filtros_sql']));
} else {
    // Si no hay datos, obtener registros del usuario actual
    $numero_empleado = $_SESSION['numero_empleado'] ?? null;

    if (!$es_admin && !$numero_empleado) {
        die("Error: No se proporcionaron datos para generar el Excel.");
    }

    $sql = "SELECT l.fecha_hora, l.tipo_fichaje, l.dispositivo, l.ip, e.nombre, e.apellidos 
            FROM log_horarios l 
            JOIN empleados e ON l.numero_empleado = e.numero_empleado";
    $filtros = [];

    if (!$es_admin) {
        $filtros[] = "l.numero_empleado = :numero_empleado";
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
        $stmt->bindParam(':numero_empleado', $numero_empleado, PDO::PARAM_INT);
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

// Crear el archivo Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Encabezados de la tabla
$sheet->setCellValue('A1', 'Nombre');
$sheet->setCellValue('B1', 'Apellidos');
$sheet->setCellValue('C1', 'Fecha y Hora');
$sheet->setCellValue('D1', 'Tipo de Fichaje');
$sheet->setCellValue('E1', 'Dispositivo');
$sheet->setCellValue('F1', 'Dirección IP');

$sheet->getStyle('A1:F1')->getFont()->setBold(true);

// Rellenar las filas con los datos
$row = 2;
foreach ($fichajes as $fichaje) {
    $sheet->setCellValue('A' . $row, $fichaje['nombre']);
    $sheet->setCellValue('B' . $row, $fichaje['apellidos']);
    $sheet->setCellValue('C' . $row, $fichaje['fecha_hora']);
    $sheet->setCellValue('D' . $row, $fichaje['tipo_fichaje']);
    $sheet->setCellValue('E' . $row, $fichaje['dispositivo']);
    $sheet->setCellValue('F' . $row, $fichaje['ip']);
    $row++;
}

// Ajustar el ancho de las columnas
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Generar el archivo Excel
$writer = new Xlsx($spreadsheet);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="historial_fichajes.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit();
