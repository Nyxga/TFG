<?php
$server = '1lbe6.h.filess.io';
$usuario = 'fichajes_sensefarm';
$password = '8966653c6e5959151328b7f391b59943754e9e87';
$dbname = 'fichajes_sensefarm';

try {
    $conexion = new PDO("mysql:host=$server; port=3307; dbname=$dbname", $usuario, $password);
} catch (Exception $error) {
    die('Error: ' . $error->getMessage());
}