<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Horarios</title>
    <link rel="stylesheet" href="./css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body id="inicio">
    <?php
    session_start();
    include './conexion.php';

    if (!isset($_SESSION['numero_empleado'])) {
        header('Location: ./index.php');
        exit();
    }

    $numero_empleado = $_SESSION['numero_empleado'];
    $semana_actual = intval((time() - strtotime('last Monday', strtotime('January 4'))) / 604800);
    $año_actual = date('Y');


    try {
        $sql = "SELECT dia_semana, hora_inicio, hora_fin 
            FROM horarios_empleados 
            WHERE numero_empleado = ? AND semana = ? AND año = ? 
            ORDER BY FIELD(dia_semana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo')";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$numero_empleado, $semana_actual, $año_actual]);
        $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die('Error al cargar los horarios: ' . htmlspecialchars($e->getMessage()));
    }

    ?>
    <header class="p-4 mb-0">
        <a href="./inicio.php">
            <h6 class="text-dark"><i class="bi bi-house-fill text-dark"></i> Volver a inicio</h6>
        </a>
    </header>

    <main class="p-4">
        <section class="text-center mb-4">
            <h1 class="fs-3">Mi Horario de la Semana <?php echo $semana_actual; ?> - <?php echo $año_actual; ?></h1>
        </section>

        <hr>

        <section class="container">
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Día</th>
                        <th>Hora Inicio</th>
                        <th>Hora Fin</th>
                        <th>Horas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalSegundos = 0;

                    if (!empty($horarios)) :
                        foreach ($horarios as $horario) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($horario['dia_semana']); ?></td>
                                <td><?php echo htmlspecialchars($horario['hora_inicio'] ?: '-'); ?></td>
                                <td><?php echo htmlspecialchars($horario['hora_fin'] ?: '-'); ?></td>
                                <td>
                                    <?php
                                    if ($horario['hora_inicio'] && $horario['hora_fin']) {
                                        $inicio = strtotime($horario['hora_inicio']);
                                        $fin = strtotime($horario['hora_fin']);
                                        $diferencia = $fin - $inicio;

                                        $totalSegundos += $diferencia;

                                        $horas = floor($diferencia / 3600);
                                        $minutos = floor(($diferencia % 3600) / 60);

                                        echo "{$horas}h {$minutos}m";
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach;

                        $totalHoras = floor($totalSegundos / 3600);
                        $totalMinutos = floor(($totalSegundos % 3600) / 60);
                        ?>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total:</td>
                            <td class="fw-bold"><?php echo "{$totalHoras}h {$totalMinutos}m"; ?></td>
                        </tr>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" class="text-center">No tienes horarios registrados para esta semana.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>