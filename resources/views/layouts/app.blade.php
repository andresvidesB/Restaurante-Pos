<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurante POS</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @livewireStyles
    
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">
    
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden bg-gray-100">
        
        <div x-show="sidebarOpen" 
             x-cloak
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden">
        </div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-800 text-white flex flex-col shadow-lg transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 lg:static lg:inset-0">
            
            <div class="h-16 flex items-center justify-center border-b border-slate-700 font-bold text-xl bg-slate-900 relative shrink-0">
                RESTO-POS
                <button @click="sidebarOpen = false" class="absolute right-4 lg:hidden text-gray-400 hover:text-white focus:outline-none p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <nav class="flex-1 px-2 py-4 space-y-2 overflow-y-auto">
                
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 rounded transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-900 text-yellow-400 shadow-inner' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Panel Principal
                </a>

                <a href="{{ route('caja') }}" class="flex items-center px-4 py-2 rounded transition-colors {{ request()->routeIs('caja') ? 'bg-slate-900 text-yellow-400 shadow-inner' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Caja / Ventas
                </a>

                <a href="{{ route('pedidos') }}" class="flex items-center px-4 py-2 rounded transition-colors {{ request()->routeIs('pedidos') ? 'bg-slate-900 text-yellow-400 shadow-inner' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    Gestión de Pedidos
                </a>

                <a href="{{ route('insumos.index') }}" class="flex items-center px-4 py-2 rounded transition-colors {{ request()->routeIs('insumos.index') ? 'bg-slate-900 text-yellow-400 shadow-inner' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    Inventario
                </a>

                <a href="{{ route('productos') }}" class="flex items-center px-4 py-2 rounded transition-colors {{ request()->routeIs('productos') ? 'bg-slate-900 text-yellow-400 shadow-inner' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Menú y Recetas
                </a>

                <a href="{{ route('reportes.index') }}" class="flex items-center px-4 py-2 rounded transition-colors {{ request()->routeIs('reportes.index') ? 'bg-slate-900 text-yellow-400 shadow-inner' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
    Reportes Contables
</a>
<a href="{{ route('mermas') }}" class="flex items-center px-4 py-2 rounded transition-colors {{ request()->routeIs('mermas') ? 'bg-slate-900 text-yellow-400 shadow-inner' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
    Mermas / Desperdicios
</a>
            </nav>
            
            <div class="p-4 border-t border-slate-700 bg-slate-900 shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="bg-slate-700 p-1 rounded-full mr-3">
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">{{ auth()->user()->name ?? 'Usuario' }}</p>
                            <p class="text-xs text-gray-400">Conectado</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-400 hover:text-red-200 hover:bg-red-900/30 p-2 rounded transition-colors" title="Cerrar Sesión">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            
            <header class="bg-white shadow-sm lg:hidden h-16 flex items-center justify-between px-4 z-10 shrink-0">
                <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none p-2 rounded hover:bg-gray-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                
                <span class="font-bold text-lg text-gray-800">RESTO-POS</span>
                
                <div class="w-8"></div> </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto p-2 md:p-6">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
    
    @livewireScripts
</body>
</html>