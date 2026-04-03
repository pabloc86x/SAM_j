<?php
session_start();
if (!isset($_SESSION['nino_id'])) {
    header('Location: index.php');
    exit();
}
$nino_id = $_SESSION['nino_id'];

// Aquí puedes obtener el nombre del niño de la base de datos si es necesario

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hub de Juegos</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Saludo personalizado -->
        <div class="col-span-2 mx-auto text-center mb-8">
            <?php
            // Aquí puedes obtener el nombre del niño desde la base de datos si es necesario
            echo "<h1 class='text-4xl font-bold'>¡Hola, [Nombre del Niño]!</h1>";
            ?>
        </div>

        <!-- Botón Letras -->
        <a href="letras.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full w-full">
            Letras
        </a>

        <!-- Botón Números -->
        <a href="numeros.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full w-full">
            Números
        </a>

        <!-- Botón Colores -->
        <a href="colores.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full w-full">
            Colores
        </a>
    </div>

    <!-- Botón Cambiar de Perfil -->
    <a href="index.php?logout=1" class="absolute bottom-4 right-4 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-full">
        Cambiar de Perfil
    </a>

</body>
</html>