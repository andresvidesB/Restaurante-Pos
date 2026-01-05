<div class="min-h-screen bg-gray-50 font-sans text-gray-800">

    <nav class="bg-white shadow-sm sticky top-0 z-40 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center cursor-pointer" wire:click="filtrarCategoria(0)">
                    <span class="text-2xl font-black text-gray-900 tracking-tighter">
                        Gourmet<span class="text-yellow-500">POS</span>
                    </span>
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'mesero')
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-sm font-bold text-white bg-slate-800 px-4 py-2 rounded-full hover:bg-slate-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Panel Interno
                            </a>
                        
                        @else
                            <div class="flex items-center gap-4">
                                <span class="text-sm font-bold text-gray-700 hidden sm:block">
                                    Hola, {{ auth()->user()->name }} 👋
                                </span>
                                
                                <button wire:click="logout" class="text-sm font-medium text-red-500 hover:text-red-700 border border-red-100 bg-red-50 px-3 py-1.5 rounded-lg transition hover:bg-red-100 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Salir
                                </button>
                            </div>
                        @endif

                    @else
                        <a href="{{ route('login') }}" class="hidden md:block text-sm font-medium text-gray-500 hover:text-gray-900">
                            Ingresar
                        </a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-full bg-black text-white text-sm font-bold shadow-lg hover:bg-gray-800 transform hover:-translate-y-0.5 transition">
                            Registrarse
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    @if($categoriaSeleccionada == 0)
    <div class="relative bg-gray-900 overflow-hidden">
        <div class="absolute inset-0 opacity-60">
            <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover">
        </div>
        <div class="relative max-w-7xl mx-auto py-24 px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-white mb-4 shadow-black drop-shadow-lg">
                El Sabor que <span class="text-yellow-400">Te Encanta</span>
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-200">
                Ingredientes frescos, recetas auténticas y ofertas increíbles todos los días.
            </p>
            <div class="mt-8 flex justify-center gap-4">
                <button wire:click="$set('categoriaSeleccionada', 0)" class="px-8 py-3 border border-transparent text-base font-medium rounded-full text-gray-900 bg-yellow-400 hover:bg-yellow-500 md:text-lg shadow-xl transition">
                    Ver Menú Completo
                </button>
            </div>
        </div>
    </div>

    @if(count($ofertas) > 0)
    <div class="max-w-7xl mx-auto px-4 py-10 sm:px-6 lg:px-8 bg-white mt-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-2 mb-6">
            <span class="text-3xl">🔥</span>
            <div>
                <h2 class="text-2xl font-black text-gray-900 uppercase italic">Ofertas del Día</h2>
                <p class="text-sm text-gray-500">Precios especiales por tiempo limitado</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($ofertas as $oferta)
                <div class="relative rounded-2xl overflow-hidden bg-white shadow-lg border border-red-100 flex group hover:shadow-2xl transition duration-300">
                    <div class="w-2/5 bg-gray-200 relative overflow-hidden">
                        @if($oferta->imagen)
                            <img src="{{ asset('storage/'.$oferta->imagen) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        @else
                             <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-400 text-xs">Sin Foto</div>
                        @endif
                        <div class="absolute top-0 left-0 bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded-br-lg uppercase tracking-wider">
                            Oferta
                        </div>
                    </div>
                    
                    <div class="w-3/5 p-4 flex flex-col justify-center">
                        <h3 class="text-lg font-bold text-gray-900 leading-tight mb-1">{{ $oferta->nombre }}</h3>
                        <p class="text-xs text-gray-500 mb-2 line-clamp-1">{{ $oferta->categoria->nombre ?? 'General' }}</p>
                        
                        <div class="flex flex-col items-start mb-3">
                            <span class="text-xs text-gray-400 line-through decoration-red-400">Antes: ${{ number_format($oferta->precio, 0) }}</span>
                            <span class="text-2xl font-black text-red-600 leading-none">${{ number_format($oferta->precio_oferta, 0) }}</span>
                        </div>

                        <button class="w-full bg-red-600 text-white py-2 rounded-lg text-sm font-bold hover:bg-red-700 transition shadow-md">
                            ¡Lo quiero!
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    @endif

    <div class="bg-gray-50 py-8" id="menu-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 sticky top-16 bg-gray-50/95 backdrop-blur z-30 py-4 mb-4">
            <div class="flex overflow-x-auto gap-3 pb-2 no-scrollbar">
                <button wire:click="filtrarCategoria(0)" 
                    class="whitespace-nowrap px-6 py-2 rounded-full text-sm font-bold border transition shadow-sm
                    {{ $categoriaSeleccionada == 0 ? 'bg-black text-white border-black' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400' }}">
                    🍽️ Todo
                </button>
                @foreach($categorias as $cat)
                    <button wire:click="filtrarCategoria({{ $cat->id }})" 
                        class="whitespace-nowrap px-6 py-2 rounded-full text-sm font-bold border transition shadow-sm
                        {{ $categoriaSeleccionada == $cat->id ? 'bg-black text-white border-black' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400' }}">
                        {{ $cat->nombre }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                {{ $categoriaSeleccionada == 0 ? 'Nuestra Carta' : 'Platos seleccionados' }}
                <span class="text-sm font-normal text-gray-400 bg-gray-200 px-2 py-0.5 rounded-full">{{ count($productos) }}</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($productos as $producto)
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 flex flex-col h-full overflow-hidden">
                        <div class="h-48 w-full bg-gray-100 relative overflow-hidden group">
                            @if($producto->imagen)
                                <img src="{{ asset('storage/'.$producto->imagen) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            
                            @if($producto->es_oferta)
                                <span class="absolute top-2 right-2 bg-yellow-400 text-black text-[10px] font-black px-2 py-1 rounded shadow-sm uppercase tracking-wider">
                                    Oferta
                                </span>
                            @endif
                        </div>

                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <p class="text-[10px] font-bold text-blue-600 mb-1 uppercase tracking-wide">
                                    {{ $producto->categoria->nombre ?? 'General' }}
                                </p>
                                <h3 class="text-lg font-bold text-gray-900 leading-tight mb-2">{{ $producto->nombre }}</h3>
                            </div>
                            
                            <div class="mt-4 flex items-end justify-between">
                                <div>
                                    @if($producto->es_oferta && $producto->precio_oferta)
                                        <div class="flex flex-col">
                                            <span class="text-xs text-gray-400 line-through">${{ number_format($producto->precio, 0) }}</span>
                                            <span class="text-xl font-black text-red-600">${{ number_format($producto->precio_oferta, 0) }}</span>
                                        </div>
                                    @else
                                        <span class="text-xl font-black text-gray-900">${{ number_format($producto->precio, 0) }}</span>
                                    @endif
                                </div>

                                <button class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition shadow-sm font-bold text-xl group">
                                    <svg class="w-5 h-5 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if(count($productos) == 0)
                <div class="text-center py-20 bg-white rounded-xl border border-dashed border-gray-300">
                    <p class="text-gray-500 text-lg">No hay productos disponibles en esta sección.</p>
                </div>
            @endif
        </div>
    </div>

    <footer class="bg-gray-900 text-white pt-16 pb-8 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
                <div>
                    <span class="text-2xl font-black tracking-tighter text-white">
                        Gourmet<span class="text-yellow-500">POS</span>
                    </span>
                    <p class="mt-4 text-gray-400 text-sm leading-relaxed">
                        Transformamos la experiencia de comer. Ingredientes de calidad, pasión en cada plato y el mejor servicio hasta tu puerta.
                    </p>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4 text-yellow-500">Enlaces Rápidos</h4>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li><button wire:click="filtrarCategoria(0)" class="hover:text-white transition">Menú Completo</button></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Iniciar Sesión</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition">Crear Cuenta</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4 text-yellow-500">Contáctanos</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-600 transition text-white">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-pink-600 transition text-white">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center">
                <p class="text-gray-500 text-xs">&copy; {{ date('Y') }} Restaurante. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <a href="https://wa.me/573000000000?text=Hola,%20quiero%20hacer%20un%20pedido" target="_blank" class="fixed bottom-6 right-6 z-50 bg-green-500 hover:bg-green-600 text-white p-4 rounded-full shadow-2xl transition hover:scale-110 flex items-center justify-center animate-bounce">
        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-8.683-2.031-9.676-.272-.099-.47-.149-.669-.149-.198 0-.42.001-.643.001-.223 0-.583.084-.89.421-.307.337-1.178 1.151-1.178 2.809 0 1.658 1.208 3.26 1.376 3.483.169.223 2.376 3.63 5.756 5.09 2.197.949 2.645.761 3.116.713.471-.048 1.511-.618 1.724-1.214.214-.595.214-1.106.149-1.214z"/></svg>
    </a>
</div>