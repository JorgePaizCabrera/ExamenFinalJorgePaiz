<?php
require 'conexion.php';

// Tipos de equipo permitidos
$tipos = ['Laptop', 'Proyector', 'Tablet', 'Cámara', 'Laboratorio'];

// Filtros
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$tipo   = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$like   = "%" . $buscar . "%";

// Consulta de equipos
if ($tipo != '' && in_array($tipo, $tipos)) {
    $sql  = "SELECT * FROM equipos WHERE (codigo LIKE ? OR nombre LIKE ?) AND tipo = ? ORDER BY id";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sss", $like, $like, $tipo);
} else {
    $sql  = "SELECT * FROM equipos WHERE (codigo LIKE ? OR nombre LIKE ?) ORDER BY id";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $like, $like);
}

$stmt->execute();
$equipos = $stmt->get_result();

include 'encabezado.php';
?>

<div class="tarjeta">

    <div class="cabecera">
        <h1>Listado de equipos</h1>
        <a href="reserva.php" class="btn btn-azul">Nueva reserva</a>
    </div>

    <!-- Filtros -->
    <form method="get" class="filtros">
        <input type="text" name="buscar" placeholder="Buscar equipo por código o nombre"
               value="<?php echo htmlspecialchars($buscar); ?>">

        <select name="tipo">
            <option value="">-- Todos los tipos --</option>
            <?php foreach ($tipos as $t) { ?>
                <option value="<?php echo $t; ?>" <?php echo ($tipo == $t) ? 'selected' : ''; ?>>
                    <?php echo $t; ?>
                </option>
            <?php } ?>
        </select>

        <button type="submit" class="btn btn-blanco">Filtrar</button>
    </form>

    <!-- Tabla de equipos -->
    <table class="tabla">
        <thead>
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Ubicación</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>

        <tbody>
            <?php if ($equipos->num_rows == 0) { ?>
                <tr>
                    <td colspan="7" class="vacio">No se encontraron equipos.</td>
                </tr>
            <?php } ?>

            <?php while ($fila = $equipos->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $fila['id']; ?></td>
                    <td><b><?php echo htmlspecialchars($fila['codigo']); ?></b></td>
                    <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                    <td><?php echo $fila['tipo']; ?></td>
                    <td><?php echo htmlspecialchars($fila['ubicacion']); ?></td>

                    <?php if ($fila['estado'] == 'Disponible') { ?>
                        <td><span class="estado disponible">Disponible</span></td>
                        <td><a href="reserva.php?equipo=<?php echo $fila['id']; ?>" class="btn btn-azul btn-chico">Reservar</a></td>
                    <?php } else { ?>
                        <td><span class="estado mantenimiento">Mantenimiento</span></td>
                        <td>-</td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</div>

<?php
$stmt->close();
$conexion->close();

include 'pie.php';
?>
