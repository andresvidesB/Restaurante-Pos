<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error del Sistema</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-900 h-screen flex flex-col items-center justify-center text-center px-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl max-w-lg border-t-8 border-red-500">
        <div class="text-6xl mb-4">😵</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">¡Ups! Algo salió mal</h1>
        <p class="text-gray-500 mb-6">El servidor encontró un error interno. Ya hemos sido notificados.</p>
        <a href="/" class="bg-slate-800 text-white px-6 py-3 rounded-lg font-bold hover:bg-slate-700 transition">
            Intentar de nuevo
        </a>
    </div>
</body>
</html>