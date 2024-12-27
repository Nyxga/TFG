<?php
require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include('log_horario.php');

$fecha_filtrada = !empty($_POST['filtrar_fecha']) ? $_POST['filtrar_fecha'] : null;
$tipo_filtrado = !empty($_POST['filtrar_tipo']) ? $_POST['filtrar_tipo'] : null;

$sql = "SELECT log_horarios.fecha_hora, log_horarios.tipo_fichaje, log_horarios.dispositivo, empleados.nombre, empleados.apellidos
    FROM log_horarios
    INNER JOIN empleados ON log_horarios.numero_empleado = empleados.numero_empleado
    WHERE log_horarios.numero_empleado = :numero_empleado";

if ($fecha_filtrada) {
    $sql .= " AND DATE(log_horarios.fecha_hora) = :fecha_filtrada";
}
if ($tipo_filtrado) {
    $sql .= " AND log_horarios.tipo_fichaje = :tipo_filtrado";
}

$sql .= " ORDER BY log_horarios.fecha_hora DESC";

$stmt = $conexion->prepare($sql);
$stmt->bindParam(':numero_empleado', $numero_empleado, PDO::PARAM_INT);

if ($fecha_filtrada) {
    $stmt->bindParam(':fecha_filtrada', $fecha_filtrada, PDO::PARAM_STR);
}
if ($tipo_filtrado) {
    $stmt->bindParam(':tipo_filtrado', $tipo_filtrado, PDO::PARAM_STR);
}

$stmt->execute();
$empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'Nombre');
$sheet->setCellValue('B1', 'Apellidos');
$sheet->setCellValue('C1', 'Fecha');
$sheet->setCellValue('D1', 'Hora');
$sheet->setCellValue('E1', 'Tipo');
$sheet->setCellValue('F1', 'Dispositivo');

$sheet->getStyle('A1:F1')->getFont()->setBold(true);

$row = 2;
foreach ($empleados as $empleado) {
    $fecha = new DateTime($empleado['fecha_hora']);
    $fecha_formateada = $fecha->format('d/m/Y');
    $hora_formateada = $fecha->format('G:i:s');

    $sheet->setCellValue('A' . $row, $empleado['nombre']);
    $sheet->setCellValue('B' . $row, $empleado['apellidos']);
    $sheet->setCellValue('C' . $row, $fecha_formateada);
    $sheet->setCellValue('D' . $row, $hora_formateada);
    $sheet->setCellValue('E' . $row, $empleado['tipo_fichaje']);
    $sheet->setCellValue('F' . $row, $empleado['dispositivo']);
    $row++;
}

$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);
$sheet->getColumnDimension('E')->setAutoSize(true);
$sheet->getColumnDimension('F')->setAutoSize(true);

$writer = new Xlsx($spreadsheet);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="historial_fichajes.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit();
?>
