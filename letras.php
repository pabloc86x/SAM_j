<?php
/**
 * ARCHIVO: letras.php
 * PROYECTO: Pequeños Genios
 * FUNCIÓN: Juego interactivo de reconocimiento de abecedario.
 */

session_start();
require_once 'conexion.php';

// Redirección si no hay un perfil seleccionado
if (!isset($_SESSION['nino_id'])) {
    header("Location: index.php");
    exit();
}

$nino_id = $_SESSION['nino_id'];

// Obtener datos del niño para personalización
$stmt = $pdo->prepare("SELECT nombre FROM ninos WHERE id_nino = ?");
$stmt->execute([$nino_id]);
$nino = $stmt->fetch();

// ID de la categoría 'Letras' según nuestro SQL (usualmente 1)
$id_categoria = 1; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego de Letras - Pequeños Genios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Quicksand:wght@500&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Quicksand', sans-serif; background-color: #f0fdf4; }
        .titulo-juego { font-family: 'Fredoka One', cursive; }
        .letra-card { transition: transform 0.2s; cursor: pointer; }
        .letra-card:hover { transform: scale(1.1); }
        .correct { background-color: #4ade80 !important; border-color: #16a34a; }
        .wrong { background-color: #f87171 !important; border-color: #dc2626; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center p-4">

    <header class="w-full max-w-4xl flex justify-between items-center mb-8">
        <a href="menu.php" class="bg-yellow-400 hover:bg-yellow-500 text-white font-bold py-2 px-4 rounded-full shadow-lg">
            ← Volver al Menú
        </a>
        <div class="text-right">
            <span class="text-xl text-green-600 font-bold">Jugando: <?php echo htmlspecialchars($nino['nombre']); ?></span>
        </div>
    </header>

    <main class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-2xl text-center border-4 border-dashed border-green-200">
        <h1 class="titulo-juego text-4xl text-green-500 mb-6">¿Qué letra es esta?</h1>
        
        <div id="target-letter" class="text-9xl text-indigo-600 font-bold mb-10 p-10 bg-indigo-50 rounded-2xl inline-block shadow-inner">
            ?
        </div>

        <div id="options-container" class="grid grid-cols-3 gap-4">
            </div>

        <div id="feedback" class="mt-8 h-8 text-2xl font-bold"></div>
    </main>

    <div id="modal-success" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white p-8 rounded-3xl text-center shadow-2xl scale-110">
            <h2 class="titulo-juego text-4xl text-yellow-500 mb-4">¡Genial! 🌟</h2>
            <p class="text-xl mb-6">Has ganado 10 puntos en Letras.</p>
            <button onclick="nextRound()" class="bg-green-500 text-white px-8 py-3 rounded-full font-bold text-xl hover:bg-green-600 shadow-lg">
                Siguiente Letra
            </button>
        </div>
    </div>

    <script>
        const alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split("");
        const idCategoria = <?php echo $id_categoria; ?>;
        let currentLetter = "";

        function initGame() {
            // Seleccionar letra aleatoria
            currentLetter = alphabet[Math.floor(Math.random() * alphabet.length)];
            document.getElementById('target-letter').innerText = currentLetter;
            
            // Generar opciones (la correcta + 2 aleatorias)
            let options = [currentLetter];
            while(options.length < 3) {
                let randomLet = alphabet[Math.floor(Math.random() * alphabet.length)];
                if(!options.includes(randomLet)) options.push(randomLet);
            }
            options.sort(() => Math.random() - 0.5);

            // Renderizar botones
            const container = document.getElementById('options-container');
            container.innerHTML = "";
            options.forEach(letra => {
                const btn = document.createElement('button');
                btn.className = "letra-card text-5xl font-bold p-6 bg-white border-4 border-indigo-200 rounded-2xl shadow-md text-indigo-500 hover:bg-indigo-50";
                btn.innerText = letra;
                btn.onclick = () => checkAnswer(letra, btn);
                container.appendChild(btn);
            });
            
            document.getElementById('feedback').innerText = "";
            document.getElementById('modal-success').classList.add('hidden');
        }

        function checkAnswer(selected, element) {
            if (selected === currentLetter) {
                element.classList.add('correct', 'text-white');
                document.getElementById('feedback').innerText = "¡Correcto! +10 puntos";
                document.getElementById('feedback').className = "mt-8 h-8 text-2xl font-bold text-green-500";
                saveProgress(10);
            } else {
                element.classList.add('wrong', 'text-white');
                document.getElementById('feedback').innerText = "¡Inténtalo de nuevo!";
                document.getElementById('feedback').className = "mt-8 h-8 text-2xl font-bold text-red-500";
                setTimeout(() => {
                    element.classList.remove('wrong', 'text-white');
                }, 1000);
            }
        }

        function saveProgress(puntos) {
            // Petición asíncrona a guardar_logro.php
            const formData = new FormData();
            formData.append('id_categoria', idCategoria);
            formData.append('puntos', puntos);

            fetch('guardar_logro.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    setTimeout(() => {
                        document.getElementById('modal-success').classList.remove('hidden');
                    }, 500);
                }
            })
            .catch(err => console.error("Error al guardar:", err));
        }

        function nextRound() {
            initGame();
        }

        // Iniciar el primer turno
        initGame();
    </script>
</body>
</html>