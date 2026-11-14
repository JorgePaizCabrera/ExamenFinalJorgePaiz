<?php
require 'conexion.php';

// Consulta de reservas con el equipo
$sql = "SELECT r.*, e.codigo, e.nombre AS equipo
        FROM reservas r
        INNER JOIN equipos e ON e.id = r.equipo_id
        ORDER BY r.fecha, r.hora_inicio";

$reservas = $conexion->query($sql);

include 'encabezado.php';
?>

<div class="tarjeta">

    <div class="cabecera">
        <h1>Listado de reservas</h1>
        <a href="reserva.php" class="btn btn-azul">Nueva reserva</a>
    </div>

    <?php if (isset($_GET['ok'])) { ?>
        <div class="alerta exito">La reserva se registró correctamente.</div>
    <?php } ?>

    <!-- Tabla de reservas -->
    <table class="tabla">
        <thead>
            <tr>
                <th>#</th>
                <th>Equipo</th>
                <th>Nombre</th>
                <th>Carné</th>
                <th>Fecha</th>
                <th>Horario</th>
                <th>Motivo</th>
            </tr>
        </thead>

        <tbody>
            <?php if ($reservas->num_rows == 0) { ?>
                <tr>
                    <td colspan="7" class="vacio">Todavía no hay reservas.</td>
                </tr>
            <?php } ?>

            <?php while ($r = $reservas->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $r['id']; ?></td>
                    <td><b><?php echo htmlspecialchars($r['codigo']); ?></b> - <?php echo htmlspecialchars($r['equipo']); ?></td>
                    <td><?php echo htmlspecialchars($r['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($r['carne']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($r['fecha'])); ?></td>
                    <td><?php echo substr($r['hora_inicio'], 0, 5) . " - " . substr($r['hora_fin'], 0, 5); ?></td>
                    <td><?php echo htmlspecialchars($r['motivo'] ?? ''); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</div>

<?php
$conexion->close();

include 'pie.php';
?>
