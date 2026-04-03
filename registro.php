<?php
// registro.php
// Formulario de registro para crear un nuevo perfil de niño.

require_once __DIR__ . '/conexion.php';

$errores = [];
$nombre = '';
$avatarSeleccionado = '';

$avatars = [
    'avatar1.png' => 'Aventurero',
    'avatar2.png' => 'Explorador',
    'avatar3.png' => 'Creativo',
    'avatar4.png' => 'Soñador',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $avatarSeleccionado = $_POST['avatar'] ?? '';

    if ($nombre === '') {
        $errores[] = 'El nombre es obligatorio.';
    }

    if ($avatarSeleccionado === '' || !array_key_exists($avatarSeleccionado, $avatars)) {
        $errores[] = 'Selecciona un avatar válido.';
    }

    if (empty($errores)) {
        try {
            $stmt = $pdo->prepare('INSERT INTO ninos (nombre, avatar) VALUES (:nombre, :avatar)');
            $stmt->execute([
                ':nombre' => $nombre,
                ':avatar' => $avatarSeleccionado,
            ]);

            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errores[] = 'No se pudo guardar el perfil. Intenta de nuevo.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registro de perfil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-amber-200 via-orange-200 to-pink-300 text-slate-900">
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="rounded-[32px] bg-white/90 p-8 shadow-2xl shadow-orange-400/20 backdrop-blur">
            <h1 class="text-3xl font-bold text-slate-900">Crear perfil de niño</h1>
            <p class="mt-2 text-slate-600">Registra un nuevo perfil para comenzar a usar la aplicación.</p>

            <?php if (!empty($errores)): ?>
                <div class="mt-6 rounded-3xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <ul class="space-y-2">
                        <?php foreach ($errores as $error): ?>
                            <li>• <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" class="mt-8 space-y-6">
                <div>
                    <label for="nombre" class="block text-sm font-semibold text-slate-700">Nombre</label>
                    <input
                        id="nombre"
                        name="nombre"
                        type="text"
                        value="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>"
                        class="mt-2 w-full rounded-3xl border border-slate-300 bg-slate-50 px-4 py-3 text-base text-slate-900 outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-200"
                        placeholder="Escribe el nombre del niño"
                        required
                    />
                </div>

                <div>
                    <p class="text-sm font-semibold text-slate-700">Elige un avatar</p>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <?php foreach ($avatars as $avatarFile => $descripcion): ?>
                            <label class="group cursor-pointer rounded-3xl border p-4 text-center transition hover:border-orange-400 hover:bg-orange-50 <?= $avatarSeleccionado === $avatarFile ? 'border-orange-500 bg-orange-100 shadow-lg shadow-orange-200/50' : 'border-slate-200 bg-white' ?>">
                                <input type="radio" name="avatar" value="<?= htmlspecialchars($avatarFile, ENT_QUOTES, 'UTF-8') ?>" class="sr-only" <?= $avatarSeleccionado === $avatarFile ? 'checked' : '' ?> />
                                <div class="mx-auto mb-3 flex h-24 w-24 items-center justify-center rounded-3xl bg-gradient-to-br from-orange-300 to-pink-300 text-4xl font-bold text-white shadow-inner shadow-orange-300/30">
                                    <span>😊</span>
                                </div>
                                <p class="font-semibold text-slate-900"><?= htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="mt-2 text-sm text-slate-500"><?= htmlspecialchars($avatarFile, ENT_QUOTES, 'UTF-8') ?></p>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="flex flex-col gap-4 pt-4 sm:flex-row sm:items-center">
                    <button type="submit" class="inline-flex min-w-[180px] items-center justify-center rounded-full bg-orange-500 px-6 py-3 text-base font-semibold text-white transition hover:bg-orange-400">Guardar perfil</button>
                    <a href="index.php" class="inline-flex min-w-[180px] items-center justify-center rounded-full border border-slate-300 bg-white px-6 py-3 text-base font-semibold text-slate-700 transition hover:bg-slate-50">Volver a inicio</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
