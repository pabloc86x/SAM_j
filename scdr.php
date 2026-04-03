<?php
try {
    // Configuración de la conexión a la base de datos
    $host = 'localhost';
    $dbname = 'pequenos_genios_db';
    $username = 'tu_usuario'; // Reemplaza con tu nombre de usuario
    $password = 'tu_contraseña'; // Reemplaza con tu contraseña

    // Crear una instancia de PDO para conectarse a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener los nombres de todas las tablas en la base de datos
    $stmt = $pdo->query('SHOW TABLES');

    // Recorrer las tablas y mostrar sus nombres
    while ($table = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo 'Tabla: ' . $table['Tables_in_' . $dbname] . '<br>';
    }

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
