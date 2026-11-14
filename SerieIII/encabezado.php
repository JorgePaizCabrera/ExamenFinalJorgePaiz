<?php
// Pagina actual para marcar el enlace activo
$pagina = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas de Equipos</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>

    <!-- Menu -->
    <header class="barra">
        <a href="index.php" class="titulo">Reservas de Equipos</a>

        <nav class="enlaces">
            <a href="index.php" class="<?php echo ($pagina == 'index.php') ? 'activo' : ''; ?>">Equipos</a>
            <a href="reservas.php" class="<?php echo ($pagina == 'reservas.php') ? 'activo' : ''; ?>">Reservas</a>
        </nav>
    </header>

    <main class="contenedor">
