<?php
$server = 'localhost';
$usuario = 'root';
$password = '';
$dbname = 'sistema_fichaje';

try {
    $conexion = new PDO("mysql:host=$server; dbname=$dbname", $usuario, $password);
} catch (Exception $error) {
    die('Error: ' . $error->getMessage());
}