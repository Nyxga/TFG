<?php
$server = "localhost";
$usuario = "root";
$password = "";
$dbname = "empleados";

try {
    $conexion = new PDO('mysql:host=$server; dbname=$dbname', '$usuario', '$password');
    echo "Conexión establecida con éxito";
} catch (Exception $error) {
    die("Error: " . $error->getMessage());
}
?>