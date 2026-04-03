<?php
// index.php
// Pantalla de inicio para seleccionar un perfil de niño.

session_start();
require_once __DIR__ . '/conexion.php';

// Si se envía un perfil, guardamos el id en la sesión y redirigimos.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nino_id'])) {
    $_SESSION['nino_id'] = (int) $_POST['nino_id'];
    header('Location: menu.php');
    exit;
}

// Consultamos todos los niños registrados.
try {
    $stmt = $pdo->query('SELECT id_nino, nombre, avatar FROM ninos ORDER BY nombre ASC');
    $ninos = $stmt->fetchAll();
} catch (PDOException $e) {
    // En producción, registre el error y muestre un mensaje genérico.
    die('No se pudo cargar la lista de niños: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Selecciona tu perfil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-cyan-400 via-blue-400 to-violet-500 text-slate-900">
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <header class="mb-10 rounded-3xl bg-white/80 p-8 shadow-2xl shadow-violet-500/20 backdrop-blur">
            <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">¡Bienvenido!</h1>
            <p class="mt-3 max-w-2xl text-lg text-slate-600">Elige el perfil del niño para comenzar la aventura.</p>
        </header>

        <?php if (!empty($ninos)): ?>
            <section class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($ninos as $nino): ?>
                    <form method="post" class="group rounded-[32px] border border-white/50 bg-white/90 p-6 shadow-xl shadow-slate-900/10 transition hover:-translate-y-1 hover:shadow-2xl">
                        <input type="hidden" name="nino_id" value="<?= htmlspecialchars($nino['id_nino'], ENT_QUOTES, 'UTF-8') ?>" />
                        <button type="submit" class="w-full text-left">
                            <div class="flex items-center gap-4">
                                <div class="flex h-24 w-24 items-center justify-center rounded-3xl bg-gradient-to-br from-fuchsia-500 to-amber-300 text-4xl text-white shadow-lg shadow-fuchsia-500/30">
                                    <?php if (!empty($nino['avatar'])): ?>
                                        <img src="<?= htmlspecialchars($nino['avatar'], ENT_QUOTES, 'UTF-8') ?>" alt="Avatar de <?= htmlspecialchars($nino['nombre'], ENT_QUOTES, 'UTF-8') ?>" class="h-24 w-24 rounded-3xl object-cover" />
                                    <?php else: ?>
                                        <span><?= strtoupper(substr($nino['nombre'], 0, 1)) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="text-lg font-semibold text-slate-900"><?= htmlspecialchars($nino['nombre'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="mt-2 text-sm text-slate-500">Toca para continuar</p>
                                </div>
                            </div>
                        </button>
                    </form>
                <?php endforeach; ?>
            </section>
        <?php else: ?>
            <section class="rounded-3xl bg-white/90 p-10 shadow-2xl shadow-slate-900/10">
                <h2 class="text-3xl font-bold text-slate-900">Aún no hay perfiles</h2>
                <p class="mt-4 text-slate-600">Crea el primer perfil y comienza a explorar el contenido educativo.</p>
                <div class="mt-8">
                    <a href="registro.php" class="inline-flex rounded-full bg-fuchsia-600 px-8 py-4 text-base font-semibold text-white shadow-lg shadow-fuchsia-600/30 transition hover:bg-fuchsia-500">Crear primer perfil</a>
                </div>
            </section>
        <?php endif; ?>
    </div>
</body>
</html>

