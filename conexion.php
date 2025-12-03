<?php
$host = "localhost";
$bd   = "bd_dmtecno";  // <-- nombre de tu base de datos
$user = "root";
$pass = "";            // coloca tu clave si tienes

try {
    $pdo = new PDO("mysql:host=$host;dbname=$bd;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
