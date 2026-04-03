<?php
/**
 * ARCHIVO: numeros.php
 * PROYECTO: Pequeños Genios
 * FUNCIÓN: Juego de conteo básico (1-10) con feedback inmediato.
 */

session_start();
require_once 'conexion.php';

// Seguridad: Verificar sesión del niño
if (!isset($_SESSION['nino_id'])) {
    header("Location: index.php");
    exit();
}

$nino_id = $_SESSION['nino_id'];

// Obtener información del perfil
$stmt = $pdo->prepare("SELECT nombre FROM ninos WHERE id_nino = ?");
$stmt->execute([$nino_id]);
$nino = $stmt->fetch();

// ID de categoría para 'Números' según nuestro SQL
$id_categoria = 2; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego de Números - Pequeños Genios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Quicksand:wght@500&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Quicksand', sans-serif; background-color: #fff7ed; }
        .titulo-juego { font-family: 'Fredoka One', cursive; }
        .number-btn { transition: all 0.2s; }
        .number-btn:hover { transform: translateY(-5px); }
        .apple-animation { animation: pop 0.5s ease-out; }
        @keyframes pop {
            0% { transform: scale(0); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center p-4">

    <header class="w-full max-w-4xl flex justify-between items-center mb-6">
        <a href="menu.php" class="bg-orange-400 hover:bg-orange-500 text-white font-bold py-2 px-6 rounded-full shadow-lg transition">
            ← Volver
        </a>
        <div class="bg-white px-4 py-2 rounded-2xl shadow-sm border-2 border-orange-100">
            <span class="text-lg text-orange-600 font-bold">¡A contar, <?php echo htmlspecialchars($nino['nombre']); ?>!</span>
        </div>
    </header>

    <main class="bg-white rounded-[3rem] shadow-xl p-8 w-full max-w-2xl text-center border-b-8 border-orange-200">
        <h1 class="titulo-juego text-3xl text-orange-500 mb-8">¿Cuántas manzanas hay?</h1>
        
        <div id="items-container" class="flex flex-wrap justify-center items-center gap-4 min-h-[180px] mb-12 p-6 bg-orange-50 rounded-3xl">
            </div>

        <div id="options-grid" class="grid grid-cols-5 gap-3">
            </div>

        <div id="message" class="mt-8 h-10 text-2xl font-bold"></div>
    </main>

    <div id="modal-win" class="hidden fixed inset-0 bg-orange-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white p-10 rounded-[3rem] text-center shadow-2xl border-4 border-orange-400">
            <div class="text-7xl mb-4">🍎✨</div>
            <h2 class="titulo-juego text-4xl text-orange-500 mb-2">¡Excelente!</h2>
            <p class="text-gray-600 text-xl mb-8">Contaste muy bien las manzanas.</p>
            <button onclick="setupGame()" class="bg-green-500 hover:bg-green-600 text-white text-2xl font-bold px-12 py-4 rounded-full shadow-xl transition-all">
                ¡Jugar otra vez!
            </button>
        </div>
    </div>

    <script>
        const idCategoria = <?php echo $id_categoria; ?>;
        let correctAnswer = 0;

        function setupGame() {
            const container = document.getElementById('items-container');
            const optionsGrid = document.getElementById('options-grid');
            const message = document.getElementById('message');
            const modal = document.getElementById('modal-win');

            // 1. Reiniciar estado
            container.innerHTML = '';
            optionsGrid.innerHTML = '';
            message.innerText = '';
            modal.classList.add('hidden');

            // 2. Generar número aleatorio entre 1 y 10
            correctAnswer = Math.floor(Math.random() * 10) + 1;

            // 3. Renderizar manzanas
            for (let i = 0; i < correctAnswer; i++) {
                const apple = document.createElement('span');
                apple.className = 'text-6xl apple-animation select-none';
                apple.innerText = '🍎';
                container.appendChild(apple);
            }

            // 4. Crear botones del 1 al 10
            for (let i = 1; i <= 10; i++) {
                const btn = document.createElement('button');
                btn.className = 'number-btn bg-white border-4 border-orange-100 hover:border-orange-400 text-orange-500 text-3xl font-bold py-4 rounded-2xl shadow-sm';
                btn.innerText = i;
                btn.onclick = () => checkResult(i, btn);
                optionsGrid.appendChild(btn);
            }
        }

        function checkResult(selected, btn) {
            if (selected === correctAnswer) {
                btn.classList.replace('border-orange-100', 'bg-green-500');
                btn.classList.add('text-white', 'border-green-600');
                
                // Guardar progreso vía AJAX
                saveProgress(10);
            } else {
                btn.classList.add('bg-red-100', 'border-red-400', 'shake-horizontal');
                document.getElementById('message').innerText = "¡Sigue contando! 🧐";
                document.getElementById('message').className = "mt-8 h-10 text-2xl font-bold text-red-400";
                
                setTimeout(() => {
                    btn.classList.remove('bg-red-100', 'border-red-400', 'shake-horizontal');
                }, 800);
            }
        }

        function saveProgress(puntos) {
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
                    document.getElementById('modal-win').classList.remove('hidden');
                }
            });
        }

        // Inicializar al cargar
        window.onload = setupGame;
    </script>

    <style>
        .shake-horizontal {
            animation: shake 0.4s cubic-bezier(.36,.07,.19,.97) both;
        }
        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }
    </style>
</body>
</html>