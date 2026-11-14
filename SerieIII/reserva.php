<?php
require 'conexion.php';

$error = "";

// Valores iniciales del formulario
$nombre      = "";
$carne       = "";
$equipo_id   = isset($_GET['equipo']) ? (int) $_GET['equipo'] : 0;
$fecha       = "";
$hora_inicio = "";
$hora_fin    = "";
$motivo      = "";

// Cuando se envia el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nombre      = trim($_POST['nombre']);
    $carne       = trim($_POST['carne']);
    $equipo_id   = (int) $_POST['equipo_id'];
    $fecha       = $_POST['fecha'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin    = $_POST['hora_fin'];
    $motivo      = trim($_POST['motivo']);

    // Validar campos
    if ($nombre == "" || $carne == "" || $equipo_id == 0 || $fecha == "" || $hora_inicio == "" || $hora_fin == "") {
        $error = "Error: todos los campos son obligatorios excepto el motivo.";
    } elseif (!preg_match('/^\d{4}-\d{4}$/', $carne)) {
        $error = "Error: ingrese un carné válido (ej. 2023-0145).";
    } elseif ($fecha < date('Y-m-d')) {
        $error = "Error: la fecha no puede ser anterior a hoy.";
    } elseif ($hora_fin <= $hora_inicio) {
        $error = "Error: la hora fin debe ser mayor que la hora inicio.";
    }

    // Validar que el equipo exista y este disponible
    if ($error == "") {
        $stmt = $conexion->prepare("SELECT estado FROM equipos WHERE id = ?");
        $stmt->bind_param("i", $equipo_id);
        $stmt->execute();
        $equipo = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$equipo) {
            $error = "Error: el equipo seleccionado no existe.";
        } elseif ($equipo['estado'] != 'Disponible') {
            $error = "Error: el equipo está en mantenimiento.";
        }
    }

    // Validar que no choque con otra reserva
    if ($error == "") {
        $sql = "SELECT fecha, hora_inicio, hora_fin FROM reservas
                WHERE equipo_id = ? AND fecha = ? AND hora_inicio < ? AND hora_fin > ?
                LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("isss", $equipo_id, $fecha, $hora_fin, $hora_inicio);
        $stmt->execute();
        $choque = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($choque) {
            $error = "Error: el equipo ya está reservado el " . $choque['fecha'] .
                     " de " . substr($choque['hora_inicio'], 0, 5) .
                     " a " . substr($choque['hora_fin'], 0, 5) . ".";
        }
    }

    // Guardar la reserva
    if ($error == "") {
        $sql = "INSERT INTO reservas (equipo_id, nombre, carne, fecha, hora_inicio, hora_fin, motivo)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("issssss", $equipo_id, $nombre, $carne, $fecha, $hora_inicio, $hora_fin, $motivo);

        if ($stmt->execute()) {
            header("Location: reservas.php?ok=1");
            exit;
        }

        $error = "Error al guardar la reserva: " . $stmt->error;
        $stmt->close();
    }
}

// Equipos disponibles para la lista
$equipos = $conexion->query("SELECT id, codigo, nombre FROM equipos WHERE estado = 'Disponible' ORDER BY codigo");

include 'encabezado.php';
?>

<div class="tarjeta formulario">

    <div class="cabecera">
        <h1>Registrar reserva</h1>
    </div>

    <?php if ($error != "") { ?>
        <div class="alerta error"><?php echo htmlspecialchars($error); ?></div>
    <?php } ?>

    <form method="post">

        <label>Nombre completo</label>
        <input type="text" name="nombre" maxlength="100" required
               value="<?php echo htmlspecialchars($nombre); ?>">

        <label>Carné</label>
        <input type="text" name="carne" maxlength="20" placeholder="2023-0145" required
               value="<?php echo htmlspecialchars($carne); ?>">
        <small>Ingrese un carné válido (ej. 2023-0145)</small>

        <label>Equipo</label>
        <select name="equipo_id" required>
            <option value="">-- Seleccione un equipo --</option>
            <?php while ($e = $equipos->fetch_assoc()) { ?>
                <option value="<?php echo $e['id']; ?>" <?php echo ($equipo_id == $e['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($e['codigo'] . " - " . $e['nombre']); ?>
                </option>
            <?php } ?>
        </select>

        <div class="fila">
            <div>
                <label>Fecha</label>
                <input type="date" name="fecha" min="<?php echo date('Y-m-d'); ?>" required
                       value="<?php echo htmlspecialchars($fecha); ?>">
            </div>

            <div>
                <label>Hora inicio</label>
                <input type="time" name="hora_inicio" required
                       value="<?php echo htmlspecialchars($hora_inicio); ?>">
            </div>

            <div>
                <label>Hora fin</label>
                <input type="time" name="hora_fin" required
                       value="<?php echo htmlspecialchars($hora_fin); ?>">
            </div>
        </div>

        <label>Motivo (opcional)</label>
        <input type="text" name="motivo" maxlength="255"
               value="<?php echo htmlspecialchars($motivo); ?>">

        <div class="botones">
            <button type="submit" class="btn btn-azul">Guardar</button>
            <a href="index.php" class="btn btn-blanco">Cancelar</a>
        </div>

    </form>

</div>

<?php
$conexion->close();

include 'pie.php';
?>
