<?php
// conexion.php
// Archivo de conexión a la base de datos mediante PDO.

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "pequenos_genios_db";
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Establece el modo de error a excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die('Error de conexión a la base de datos: ' . $e->getMessage());
}

// $pdo está listo para usarse en el resto de la aplicación.
return $pdo;

