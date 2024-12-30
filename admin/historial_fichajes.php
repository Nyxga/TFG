<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial fichajes</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body id="historial_fichajes">
    <?php
    include '../conexion.php';

    $nombre_filtrado = $_POST['filtrar_nombre'] ?? '';
    $fecha_filtrada = $_POST['filtrar_fecha'] ?? '';
    $tipo_filtrado = $_POST['filtrar_tipo'] ?? '';

    try {
        $sql = "SELECT l.id, l.fecha_hora, l.tipo_fichaje, l.dispositivo, l.ip, e.nombre, e.apellidos 
                FROM log_horarios l 
                JOIN empleados e ON l.numero_empleado = e.numero_empleado";
        $filtros = [];

        if (!empty($nombre_filtrado)) {
            $filtros[] = "e.nombre LIKE :nombre_filtrado";
        }
        if (!empty($fecha_filtrada)) {
            $filtros[] = "DATE(l.fecha_hora) = :fecha_filtrada";
        }
        if (!empty($tipo_filtrado)) {
            $filtros[] = "l.tipo_fichaje = :tipo_filtrado";
        }

        if (!empty($filtros)) {
            $sql .= " WHERE " . implode(" AND ", $filtros);
        }

        $sql .= " ORDER BY l.fecha_hora DESC";
        $stmt = $conexion->prepare($sql);

        if (!empty($nombre_filtrado)) {
            $stmt->bindValue(':nombre_filtrado', "%$nombre_filtrado%", PDO::PARAM_STR);
        }
        if (!empty($fecha_filtrada)) {
            $stmt->bindValue(':fecha_filtrada', $fecha_filtrada, PDO::PARAM_STR);
        }
        if (!empty($tipo_filtrado)) {
            $stmt->bindValue(':tipo_filtrado', $tipo_filtrado, PDO::PARAM_STR);
        }

        $stmt->execute();
        $fichajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger" role="alert">Error al cargar el historial: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    ?>

    <header class="p-4 mb-0">
        <a href="./inicio.php">
            <h6 class="text-dark"><i class="bi bi-house-fill text-dark"></i> Volver a inicio</h6>
        </a>
    </header>

    <main class="p-4">
        <section class="d-flex justify-content-center">
            <h1 class="fs-3 mt-4">Historial de fichajes</h1>
        </section>

        <hr>

        <section class="d-flex mt-4 text-center justify-content-center">
            <div class="table-responsive">
                <form method="POST" action="./historial_fichajes.php" id="form_filtrar">
                    <table>
                        <thead>
                            <tr>
                                <td><span><i class="bi bi-person-badge"></i> Filtrar nombre</span></td>
                                <td><span class="mx-4"><i class="bi bi-calendar"></i> Filtrar fecha</span></td>
                                <td><span class="mx-4"><i class="bi bi-funnel-fill"></i> Filtrar tipo</span></td>
                            </tr>
                            <tr>
                                <td><input id="filtrar_nombre" type="text" name="filtrar_nombre" class="rounded border text-center" placeholder="Nombre empleado" value="<?php echo htmlspecialchars($nombre_filtrado); ?>"></td>
                                <td><input id="filtrar_fecha" type="date" name="filtrar_fecha" class="rounded border text-center" value="<?php echo htmlspecialchars($fecha_filtrada); ?>"></td>
                                <td>
                                    <select name="filtrar_tipo" class="form-select">
                                        <option value="" selected disabled hidden>Tipo</option>
                                        <option value="Entrada" <?php echo ($tipo_filtrado === 'Entrada') ? 'selected' : ''; ?>>Entrada</option>
                                        <option value="Salida" <?php echo ($tipo_filtrado === 'Salida') ? 'selected' : ''; ?>>Salida</option>
                                        <option value="Inicio Descanso" <?php echo ($tipo_filtrado === 'Inicio Descanso') ? 'selected' : ''; ?>>Inicio descanso</option>
                                        <option value="Fin Descanso" <?php echo ($tipo_filtrado === 'Fin Descanso') ? 'selected' : ''; ?>>Fin descanso</option>
                                    </select>
                                </td>
                            </tr>
                        </thead>
                    </table>
                    <div class="mt-3 d-flex justify-content-center">
                        <button type="submit" name="buscar" class="btn btn-primary mx-2">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                        <button type="button" class="btn btn-danger mx-2" onclick="window.location.href='./historial_fichajes.php'">
                            <i class="bi bi-arrow-clockwise"></i> Reiniciar
                        </button>
                    </div>
                </form>
                <form method="POST" action="../generar_excel.php" class="d-flex justify-content-center mt-4">
                    <?php if (!empty($fichajes)): ?>
                        <input type="hidden" name="filtros_sql" value="<?php echo base64_encode(serialize($fichajes)); ?>">
                    <?php endif; ?>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel"></i> Generar EXCEL
                    </button>
                </form>
            </div>
        </section>

        <section class="d-flex text-center justify-content-center mt-4 mb-4">
            <?php
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error_message'] . '</div>';
                unset($_SESSION['error_message']);
            }

            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success" role="alert">' . $_SESSION['success_message'] . '</div>';
                unset($_SESSION['success_message']);
            }
            ?>
        </section>

        <hr>

        <section>
            <div class="table-responsive">
                <table class="table table-striped text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Fecha y Hora</th>
                            <th>Tipo de Fichaje</th>
                            <th>Dispositivo</th>
                            <th>Dirección IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($fichajes)) : ?>
                            <?php foreach ($fichajes as $fichaje) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($fichaje['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($fichaje['apellidos']); ?></td>
                                    <td>
                                        <form action="editar_fichaje.php" method="POST" class="d-flex">
                                            <input type="datetime-local" name="fecha_hora" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($fichaje['fecha_hora']))); ?>" class="form-control">
                                            <input type="hidden" name="id_fichaje" value="<?php echo htmlspecialchars($fichaje['id']); ?>">
                                            <input type="hidden" name="filtrar_nombre" value="<?php echo htmlspecialchars($nombre_filtrado); ?>">
                                            <input type="hidden" name="filtrar_fecha" value="<?php echo htmlspecialchars($fecha_filtrada); ?>">
                                            <input type="hidden" name="filtrar_tipo" value="<?php echo htmlspecialchars($tipo_filtrado); ?>">
                                            <button type="submit" class="btn btn-sm btn-success mx-2">Guardar</button>
                                        </form>
                                    </td>
                                    <td><?php echo htmlspecialchars($fichaje['tipo_fichaje']); ?></td>
                                    <td><?php echo htmlspecialchars($fichaje['dispositivo']); ?></td>
                                    <td><?php echo htmlspecialchars($fichaje['ip']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6" class="text-center">No se encontraron registros.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>