<?php
session_start();
require_once 'conexion.php';

// Comprobar si hay un niño activo
if (!isset($_SESSION['id_nino'])) {
    header('Location: login.php'); // Redirigir a la página de inicio de sesión si no está autenticado
    exit;
}

$id_nino = $_SESSION['id_nino'];

// Conectar a la base de datos
$conexion = new mysqli($host, $user, $pass, $dbname);

if ($conexion->connect_error) {
    die("Error al conectar: " . $conexion->connect_error);
}

// Función para guardar el logro y sumar puntos
function guardar_logro($id_nino, $color_resultante) {
    global $conexion;
    
    // Verificar si ya existe un logro con el mismo niño y color
    $query = "SELECT * FROM progreso WHERE id_nino = ? AND color_resultante = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("is", $id_nino, $color_resultante);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // No existe el logro, insertar nuevo
        $query = "INSERT INTO progreso (id_nino, color_resultante, puntos) VALUES (?, ?, 10)";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("is", $id_nino, $color_resultante);
        $stmt->execute();
    } else {
        // Existe el logro, actualizar puntos
        $query = "UPDATE progreso SET puntos = puntos + 10 WHERE id_nino = ? AND color_resultante = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("is", $id_nino, $color_resultante);
        $stmt->execute();
    }
}

// Manejo del formulario de mezcla
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $color1 = $_POST['color1'];
    $color2 = $_POST['color2'];

    // Determinar el color resultante
    if ($color1 == "rojo" && $color2 == "azul") {
        $color_resultante = "morado";
    } elseif ($color1 == "rojo" && $color2 == "amarillo") {
        $color_resultante = "naranja";
    } elseif ($color1 == "azul" && $color2 == "amarillo") {
        $color_resultante = "verde";
    } else {
        $color_resultante = "";
    }

    if (!empty($color_resultante)) {
        guardar_logro($id_nino, $color_resultante);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mezclador Mágico de Colores</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/confetti-js@0.5.0/dist/confetti.min.js"></script>
    <style>
        .color-palette {
            display: flex;
            justify-content: space-around;
            margin-top: 2rem;
        }
        .color-option {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
        }
        .color-option:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="bg-gray-200 flex items-center justify-center min-h-screen">
    <div class="container bg-white p-8 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold mb-4 text-center">Mezclador Mágico de Colores</h1>
        
        <?php if (isset($color_resultante) && !empty($color_resultante)): ?>
            <div id="result" class="mt-6 p-4 bg-green-200 rounded-lg shadow-md">
                ¡Felicidades! Has mezclado correctamente: <span class="font-bold"><?php echo ucfirst($color_resultante); ?></span>
            </div>
        <?php endif; ?>

        <form action="colores.php" method="post" id="mixForm">
            <div class="flex justify-center mb-6">
                <div class="color-option bg-red-500 color1"></div>
                <div class="color-option bg-blue-500 color2"></div>
            </div>
            <div id="resultContainer" class="hidden">
                <div id="largeColor" class="w-32 h-32 bg-white rounded-full shadow-lg animate-bounce mx-auto mt-4 cursor-pointer">
                    <!-- Color resultante aparecerá aquí -->
                </div>
            </div>
        </form>

        <div class="color-palette">
            <?php
            $colores = ['rojo', 'azul', 'amarillo'];
            foreach ($colores as $color): ?>
                <div id="<?php echo $color; ?>" class="color-option bg-<?php echo $color; ?>-500" data-color="<?php echo $color; ?>"></div>
            <?php endforeach; ?>
        </div>

        <script>
            const colorOptions = document.querySelectorAll('.color-option');
            const largeColor = document.getElementById('largeColor');
            const resultContainer = document.getElementById('resultContainer');

            colorOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const selectedColor = this.getAttribute('data-color');
                    if (document.querySelector('.color1')) {
                        // Si ya hay un color seleccionado, mezclar
                        largeColor.classList.remove('bg-white');
                        largeColor.style.backgroundColor = `linear-gradient(to right, ${selectedColor}, #fff)`;
                        document.getElementById('resultContainer').classList.remove('hidden');

                        // Enviar la mezcla al servidor
                        const form = document.getElementById('mixForm');
                        form.color1.value = document.querySelector('.color1').getAttribute('data-color');
                        form.color2.value = selectedColor;
                        form.submit();
                    } else {
                        // Si no hay color seleccionado, agregar el primero
                        this.classList.add('color1');
                        largeColor.style.backgroundColor = selectedColor;
                    }
                });
            });

            // Lluvia de confeti cuando se mezcla correctamente
            document.getElementById('resultContainer').addEventListener('click', function() {
                confetti.create(document.body, { spread: 360, particleCount: 50, origin: { y: 0.6 } });
            });
        </script>
    </div>

</body>
</html>
