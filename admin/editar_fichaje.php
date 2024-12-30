<?php
include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_fichaje = $_POST['id_fichaje'];
    $fecha_hora = $_POST['fecha_hora'];

    try {
        $stmt = $conexion->prepare("UPDATE log_horarios SET fecha_hora = ? WHERE id = ?");
        $stmt->execute([$fecha_hora, $id_fichaje]);

        header('Location: ./historial_fichajes.php');
    } catch (PDOException $e) {
        header('Location: ./historial_fichajes.php');
    }
}
