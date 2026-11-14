<?php
// Datos de conexion
$servidor  = "localhost";
$usuario   = "root";
$clave     = "";
$basedatos = "reservas_equipos";

// Crear conexion
$conexion = new mysqli($servidor, $usuario, $clave, $basedatos);

// Verificar conexion
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
