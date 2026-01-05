<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Gourmet Carbón') }}</title>
    
    <style>
        /* Fondo de carbón con textura */
        .bg-charcoal {
            background-color: #1a1a1a;
            background-image: url("https://www.transparenttextures.com/patterns/dark-matter.png");
        }

        /* Efecto de Chispas Flotantes */
        .sparks-container {
            position: fixed; /* Cambiado a fixed para cubrir toda la pantalla */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }

        .spark {
            position: absolute;
            width: 3px;
            height: 3px;
            background: #ff7b00;
            border-radius: 50%;
            filter: blur(1px);
            animation: fly 6s infinite ease-in;
            opacity: 0;
        }

        @keyframes fly {
            0% { transform: translateY(100vh) translateX(0); opacity: 1; }
            50% { opacity: 0.8; }
            100% { transform: translateY(-10vh) translateX(50px); opacity: 0; }
        }

        /* Estilo para los inputs de Login/Registro para que combinen */
        input {
            background-color: #262626 !important;
            border-color: #444 !important;
            color: white !important;
        }
        
        input:focus {
            border-color: #ff7b00 !important;
            box-shadow: 0 0 0 1px #ff7b00 !important;
        }
    </style>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-charcoal font-sans antialiased text-gray-200 min-h-screen flex flex-col justify-center">
    
    <div class="sparks-container">
        <div class="spark" style="left:10%; animation-delay:0s;"></div>
        <div class="spark" style="left:30%; animation-delay:2s;"></div>
        <div class="spark" style="left:50%; animation-delay:1s;"></div>
        <div class="spark" style="left:70%; animation-delay:4s;"></div>
        <div class="spark" style="left:90%; animation-delay:3s;"></div>
        <div class="spark" style="left:20%; animation-delay:5s;"></div>
        <div class="spark" style="left:80%; animation-delay:1.5s;"></div>
    </div>

    <div class="relative z-10 w-full">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>