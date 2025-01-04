<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Horarios</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php
    include '../conexion.php';

    $usernameSeleccionado = $_POST['username'] ?? null;
    $semanaSeleccionada = $_POST['semana'] ?? null;
    $horarios = [];

    if ($usernameSeleccionado && $semanaSeleccionada && isset($_POST['cargar_horarios'])) {
        $semana = date('W', strtotime($semanaSeleccionada));
        $año = date('o', strtotime($semanaSeleccionada));

        $sql = "SELECT dia_semana, hora_inicio, hora_fin 
                FROM horarios_empleados he
                INNER JOIN empleados e ON he.numero_empleado = e.numero_empleado
                WHERE e.username = ? AND he.semana = ? AND he.año = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$usernameSeleccionado, $semana, $año]);
        $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $horariosFormateados = [];
        foreach ($horarios as $horario) {
            $horariosFormateados[$horario['dia_semana']] = [
                'inicio' => $horario['hora_inicio'],
                'fin' => $horario['hora_fin']
            ];
        }
    }

    $empleados = [];
    $sql = "SELECT username, CONCAT(nombre, ' ', apellidos) AS nombre_completo FROM empleados";
    $stmt = $conexion->query($sql);
    while ($empleado = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $empleados[] = $empleado;
    }
    ?>

    <header class="p-4 mb-0">
        <a href="./inicio.php">
            <h6 class="text-dark"><i class="bi bi-house-fill text-dark"></i> Volver a inicio</h6>
        </a>
    </header>

    <main class="p-4">
        <section class="text-center mb-4">
            <h1 class="fs-3">Gestión de Horarios Semanales</h1>
        </section>

        <hr>

        <section class="container">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="semana" class="form-label">Seleccionar Semana</label>
                    <input type="week" name="semana" id="semana" class="form-control" value="<?php echo htmlspecialchars($semanaSeleccionada ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Buscar Empleado</label>
                    <input list="lista_empleados" name="username" id="username" class="form-control" required placeholder="Escriba el nombre del empleado">
                    <datalist id="lista_empleados">
                        <?php foreach ($empleados as $empleado) : ?>
                            <option value="<?php echo htmlspecialchars($empleado['username']); ?>"><?php echo htmlspecialchars($empleado['nombre_completo']); ?></option>
                        <?php endforeach; ?>
                    </datalist>
                </div>

                <div class="d-flex mt-4 mb-4">
                    <button type="submit" name="cargar_horarios" class="btn btn-primary">Cargar Horarios</button>
                    <button type="submit" formaction="guardar_horarios.php" class="btn btn-success mx-2">Guardar Horarios</button>
                </div>

                <table class="table table-bordered text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Día</th>
                            <th>Hora Inicio</th>
                            <th>Hora Fin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dias_semana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                        foreach ($dias_semana as $dia) :
                            $inicio = $horariosFormateados[$dia]['inicio'] ?? '';
                            $fin = $horariosFormateados[$dia]['fin'] ?? '';
                        ?>
                            <tr>
                                <td><strong><?php echo $dia; ?></strong></td>
                                <td>
                                    <input type="time" name="horarios[<?php echo $dia; ?>][inicio]" id="hora_inicio_<?php echo strtolower($dia); ?>" class="form-control" value="<?php echo htmlspecialchars($inicio); ?>">
                                </td>
                                <td>
                                    <input type="time" name="horarios[<?php echo $dia; ?>][fin]" id="hora_fin_<?php echo strtolower($dia); ?>" class="form-control" value="<?php echo htmlspecialchars($fin); ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>