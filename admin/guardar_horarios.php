<?php
session_start();
include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $horarios = $_POST['horarios'];
    $semana = date('W', strtotime($_POST['semana']));
    $año = date('o', strtotime($_POST['semana']));

    try {
        $stmt = $conexion->prepare("SELECT numero_empleado FROM empleados WHERE username = ?");
        $stmt->execute([$username]);
        $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$empleado) {
            throw new Exception("El usuario especificado no existe.");
        }

        $numero_empleado = $empleado['numero_empleado'];

        $stmt = $conexion->prepare("SELECT dia_semana, hora_inicio, hora_fin FROM horarios_empleados WHERE numero_empleado = ? AND semana = ? AND año = ?");
        $stmt->execute([$numero_empleado, $semana, $año]);
        $horarios_existentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $horarios_combinados = [];
        foreach ($horarios_existentes as $horario) {
            $horarios_combinados[$horario['dia_semana']] = [
                'inicio' => $horario['hora_inicio'],
                'fin' => $horario['hora_fin'],
            ];
        }

        foreach ($horarios as $dia => $horario) {
            $hora_inicio = $horario['inicio'] ?: ($horarios_combinados[$dia]['inicio'] ?? null);
            $hora_fin = $horario['fin'] ?: ($horarios_combinados[$dia]['fin'] ?? null);
            $horarios_combinados[$dia] = ['inicio' => $hora_inicio, 'fin' => $hora_fin];
        }

        foreach ($horarios_combinados as $dia => $horario) {
            $hora_inicio = $horario['inicio'];
            $hora_fin = $horario['fin'];

            $stmt = $conexion->prepare("INSERT INTO horarios_empleados (numero_empleado, dia_semana, hora_inicio, hora_fin, semana, año) 
                                            VALUES (?, ?, ?, ?, ?, ?)
                                            ON DUPLICATE KEY UPDATE hora_inicio = VALUES(hora_inicio), hora_fin = VALUES(hora_fin)");
            $stmt->execute([$numero_empleado, $dia, $hora_inicio, $hora_fin, $semana, $año]);
        }

        $_SESSION['success_message'] = 'Cambios guardados correctamente.';
        header('Location: ./establecer_horarios.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Error al guardar los horarios: ' . $e->getMessage();
        header('Location: ./establecer_horarios.php');
        exit();
    }
}
