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
            position: fixed;
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

        /* Inputs estilizados */
        input, textarea, select {
            background-color: #262626 !important;
            border-color: #444 !important;
            color: white !important;
        }
        
        input:focus, textarea:focus, select:focus {
            border-color: #ff7b00 !important;
            box-shadow: 0 0 0 1px #ff7b00 !important;
        }
    </style>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-charcoal font-sans antialiased text-gray-200 min-h-screen flex flex-col selection:bg-orange-500 selection:text-white">
    
    {{-- Animación de Fondo --}}
    <div class="sparks-container">
        <div class="spark" style="left:10%; animation-delay:0s;"></div>
        <div class="spark" style="left:30%; animation-delay:2s;"></div>
        <div class="spark" style="left:50%; animation-delay:1s;"></div>
        <div class="spark" style="left:70%; animation-delay:4s;"></div>
        <div class="spark" style="left:90%; animation-delay:3s;"></div>
        <div class="spark" style="left:20%; animation-delay:5s;"></div>
        <div class="spark" style="left:80%; animation-delay:1.5s;"></div>
    </div>

    {{-- Contenido Principal --}}
    <main class="relative z-10 w-full flex-1 flex flex-col">
        {{ $slot }}
    </main>

    {{-- Footer Global (Redes y Créditos) --}}
    <footer class="relative z-10 bg-black/80 backdrop-blur-md border-t border-white/5 py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-6">
            
            {{-- Redes Sociales --}}
            <div class="flex items-center gap-6">
                <a href="https://www.facebook.com/share/1G5RBQGBy8/" target="_blank" class="group transition duration-300">
                    <svg class="w-6 h-6 text-gray-500 group-hover:text-[#1877F2] transition transform group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                
                <a href="https://www.instagram.com/andresbvides?igsh=c3IxZWoyNG8yc2hk" target="_blank" class="group transition duration-300">
                    <svg class="w-6 h-6 text-gray-500 group-hover:text-[#E1306C] transition transform group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                </a>

                <a href="https://wa.me/573137163216" target="_blank" class="group transition duration-300">
                    <svg class="w-6 h-6 text-gray-500 group-hover:text-[#25D366] transition transform group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-8.683-2.031-9.676-.272-.099-.47-.149-.669-.149-.198 0-.42.001-.643.001-.223 0-.583.084-.89.421-.307.337-1.178 1.151-1.178 2.809 0 1.658 1.208 3.26 1.376 3.483.169.223 2.376 3.63 5.756 5.09 2.197.949 2.645.761 3.116.713.471-.048 1.511-.618 1.724-1.214.214-.595.214-1.106.149-1.214z"/></svg>
                </a>
            </div>

            {{-- Créditos y Copyright --}}
            <div class="text-center md:text-right">
                <p class="text-xs text-gray-500 mb-1">&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
                <div class="text-[10px] text-gray-600 uppercase tracking-widest">
                    Developer By 
                    <a href="https://nexoratech.com" target="_blank" class="text-orange-600 font-black hover:text-orange-400 transition">
                        Nexora Tech
                    </a>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>