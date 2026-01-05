<div class="min-h-screen bg-[#1a1a1a] font-sans text-gray-200" style="background-image: url('https://www.transparenttextures.com/patterns/dark-matter.png');">
    
    <style>
        @keyframes fly {
            0% { transform: translateY(100%) translateX(0); opacity: 1; }
            50% { opacity: 0.8; }
            100% { transform: translateY(-100%) translateX(50px); opacity: 0; }
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
            pointer-events: none;
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>

    <nav class="bg-[#121212]/90 backdrop-blur-md border-b border-white/10 shadow-2xl sticky top-0 z-40 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center cursor-pointer" wire:click="filtrarCategoria(0)">
                    <span class="text-2xl font-black text-white tracking-tighter">
                        Gourmet<span class="text-orange-500 italic">CARBÓN</span>
                    </span>
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'mesero')
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-sm font-bold text-white bg-orange-600 px-4 py-2 rounded-full hover:bg-orange-700 transition shadow-lg shadow-orange-900/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Panel Interno
                            </a>
                        @else
                            <div class="flex items-center gap-4">
                                <span class="text-sm font-bold text-gray-300 hidden sm:block">Hola, {{ auth()->user()->name }} 👋</span>
                                <button wire:click="logout" class="text-sm font-medium text-orange-500 hover:text-orange-400 border border-orange-500/30 bg-orange-500/5 px-3 py-1.5 rounded-lg transition hover:bg-orange-500/10 flex items-center gap-1">
                                    Salir
                                </button>
                            </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="hidden md:block text-sm font-medium text-gray-400 hover:text-white">Ingresar</a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-full bg-orange-600 text-white text-sm font-bold shadow-lg hover:bg-orange-700 transition transform hover:-translate-y-0.5">Registrarse</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    @if($categoriaSeleccionada == 0)
    <div class="relative h-[70vh] flex items-center justify-center bg-black overflow-hidden">
        <div class="absolute inset-0 z-10 overflow-hidden pointer-events-none">
            @for($i=0; $i<25; $i++)
                <div class="spark" style="left:{{rand(0,100)}}%; animation-delay:{{rand(0,5)}}s; width:{{rand(2,5)}}px; height:{{rand(2,5)}}px;"></div>
            @endfor
        </div>

        <div class="absolute inset-0 opacity-40">
            <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?q=80&w=1974&auto=format&fit=crop" class="w-full h-full object-cover">
        </div>
        
        <div class="relative z-20 max-w-7xl mx-auto py-24 px-4 text-center">
            <h1 class="text-5xl md:text-8xl font-black tracking-tighter text-white mb-4 uppercase italic drop-shadow-[0_5px_15px_rgba(0,0,0,1)]">
                Directo al <span class="text-orange-600">Carbón</span>
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-300 font-light italic">
                Sabor ahumado, recetas ancestrales y la mejor experiencia en cada bocado.
            </p>
            <div class="mt-10 flex justify-center gap-4">
                <button wire:click="$set('categoriaSeleccionada', 0)" class="px-10 py-4 bg-orange-600 text-white text-lg font-black rounded-full shadow-[0_0_20px_rgba(234,88,12,0.4)] hover:bg-orange-700 transition transform hover:scale-105 uppercase tracking-widest">
                    Ver La Carta
                </button>
            </div>
        </div>
        <div class="absolute bottom-0 w-full h-32 bg-gradient-to-t from-[#1a1a1a] to-transparent z-10"></div>
    </div>

    @if(count($ofertas) > 0)
    <div class="max-w-7xl mx-auto px-4 py-10 sm:px-6 lg:px-8 bg-[#222] mt-[-64px] rounded-3xl shadow-2xl border border-white/5 relative z-20">
        <div class="flex items-center gap-2 mb-8">
            <span class="text-3xl animate-pulse">🔥</span>
            <div>
                <h2 class="text-2xl font-black text-white uppercase italic tracking-wider">Parrilla en Oferta</h2>
                <p class="text-sm text-gray-500 uppercase font-bold tracking-widest">Aprovecha antes de que se apaguen las brasas</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($ofertas as $oferta)
                <div class="relative rounded-2xl overflow-hidden bg-[#161616] shadow-xl border border-white/5 flex group hover:border-orange-500/50 transition duration-500">
                    <div class="w-2/5 bg-neutral-900 relative overflow-hidden">
                        @if($oferta->imagen)
                            <img src="{{ asset('storage/'.$oferta->imagen) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                        @else
                             <div class="w-full h-full flex items-center justify-center text-gray-600 text-[10px] font-bold">GRILL IMAGE</div>
                        @endif
                        <div class="absolute top-0 left-0 bg-orange-600 text-white text-[10px] font-black px-3 py-1 rounded-br-lg uppercase tracking-widest">SALE</div>
                    </div>
                    
                    <div class="w-3/5 p-5 flex flex-col justify-center">
                        <h3 class="text-xl font-bold text-white leading-tight mb-1 italic">{{ $oferta->nombre }}</h3>
                        <p class="text-[10px] text-orange-500 font-black uppercase mb-3 tracking-widest">{{ $oferta->categoria->nombre ?? 'BRASA' }}</p>
                        
                        <div class="flex flex-col items-start mb-4">
                            <span class="text-xs text-gray-500 line-through decoration-orange-600/50">${{ number_format($oferta->precio, 0) }}</span>
                            <span class="text-3xl font-black text-orange-500 leading-none">${{ number_format($oferta->precio_oferta, 0) }}</span>
                        </div>

                        <button class="w-full bg-white text-black py-2.5 rounded-xl text-xs font-black uppercase hover:bg-orange-600 hover:text-white transition duration-300 shadow-lg">
                            ¡Lo quiero!
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    @endif

    <div class="py-12" id="menu-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 sticky top-16 bg-[#1a1a1a]/95 backdrop-blur z-30 py-6 mb-4">
            <div class="flex overflow-x-auto gap-4 pb-2 no-scrollbar">
                <button wire:click="filtrarCategoria(0)" 
                    class="whitespace-nowrap px-8 py-3 rounded-full text-xs font-black uppercase tracking-widest transition-all duration-300
                    {{ $categoriaSeleccionada == 0 ? 'bg-orange-600 text-white shadow-[0_0_15px_rgba(234,88,12,0.4)]' : 'bg-white/5 text-gray-400 hover:bg-white/10' }}">
                    🔥 Todo
                </button>
                @foreach($categorias as $cat)
                    <button wire:click="filtrarCategoria({{ $cat->id }})" 
                        class="whitespace-nowrap px-8 py-3 rounded-full text-xs font-black uppercase tracking-widest transition-all duration-300
                        {{ $categoriaSeleccionada == $cat->id ? 'bg-orange-600 text-white shadow-[0_0_15px_rgba(234,88,12,0.4)]' : 'bg-white/5 text-gray-400 hover:bg-white/10' }}">
                        {{ $cat->nombre }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
            <h2 class="text-2xl font-black text-white mb-8 flex items-center gap-3 uppercase italic tracking-tighter">
                {{ $categoriaSeleccionada == 0 ? 'Nuestra Parrilla' : 'Cortes Seleccionados' }}
                <span class="text-xs font-bold text-gray-500 border border-white/10 px-3 py-1 rounded-full">{{ count($productos) }}</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($productos as $producto)
                    <div class="bg-[#222] rounded-3xl shadow-xl border border-white/5 hover:border-orange-500/30 transition-all duration-500 transform hover:-translate-y-2 flex flex-col h-full overflow-hidden group">
                        <div class="h-56 w-full bg-neutral-900 relative overflow-hidden">
                            @if($producto->imagen)
                                <img src="{{ asset('storage/'.$producto->imagen) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700 opacity-90 group-hover:opacity-100">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-neutral-800">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            
                            @if($producto->es_oferta)
                                <span class="absolute top-4 right-4 bg-orange-600 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-tighter shadow-xl animate-bounce">
                                    ¡SALE!
                                </span>
                            @endif
                        </div>

                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <p class="text-[10px] font-black text-orange-500 mb-2 uppercase tracking-[0.2em]">{{ $producto->categoria->nombre ?? 'GRILL' }}</p>
                                <h3 class="text-xl font-bold text-white italic tracking-tight mb-2 group-hover:text-orange-500 transition-colors">{{ $producto->nombre }}</h3>
                            </div>
                            
                            <div class="mt-6 flex items-center justify-between">
                                <div>
                                    @if($producto->es_oferta && $producto->precio_oferta)
                                        <div class="flex flex-col">
                                            <span class="text-[10px] text-gray-500 line-through italic">${{ number_format($producto->precio, 0) }}</span>
                                            <span class="text-2xl font-black text-white tracking-tighter">${{ number_format($producto->precio_oferta, 0) }}</span>
                                        </div>
                                    @else
                                        <span class="text-2xl font-black text-white tracking-tighter">${{ number_format($producto->precio, 0) }}</span>
                                    @endif
                                </div>

                                <button class="w-12 h-12 rounded-2xl bg-orange-600 text-white hover:bg-orange-500 flex items-center justify-center transition shadow-lg shadow-orange-900/40 group-active:scale-90">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <footer class="bg-black text-gray-400 pt-20 pb-10 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-16 mb-16 text-center md:text-left">
                <div>
                    <span class="text-3xl font-black tracking-tighter text-white uppercase italic">
                        Gourmet<span class="text-orange-600">CARBÓN</span>
                    </span>
                    <p class="mt-6 text-sm leading-loose italic">
                        Pasión por el fuego y respeto por el ingrediente. La mejor parrilla artesanal servida con elegancia.
                    </p>
                </div>
                <div>
                    <h4 class="font-black text-white uppercase tracking-widest text-sm mb-6">Explora</h4>
                    <ul class="space-y-4 text-xs font-bold uppercase tracking-widest">
                        <li><button wire:click="filtrarCategoria(0)" class="hover:text-orange-500 transition">La Carta</button></li>
                        <li><a href="{{ route('login') }}" class="hover:text-orange-500 transition">Mi Perfil</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-black text-white uppercase tracking-widest text-sm mb-6">El Fuego</h4>
                    <div class="flex justify-center md:justify-start space-x-6">
                        <a href="#" class="hover:text-orange-500 transition"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                        <a href="#" class="hover:text-orange-500 transition"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-white/5 pt-10 text-center text-[10px] font-bold uppercase tracking-[0.3em] text-gray-700">
                &copy; {{ date('Y') }} DEVELOPED BY: NEXORA TECH.
            </div>
        </div>
    </footer>

    <a href="https://wa.me/573000000000" target="_blank" class="fixed bottom-6 right-6 z-50 bg-green-600 hover:bg-green-700 text-white p-4 rounded-2xl shadow-[0_10px_30px_rgba(0,0,0,0.5)] transition hover:scale-110 flex items-center justify-center animate-bounce">
        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-8.683-2.031-9.676-.272-.099-.47-.149-.669-.149-.198 0-.42.001-.643.001-.223 0-.583.084-.89.421-.307.337-1.178 1.151-1.178 2.809 0 1.658 1.208 3.26 1.376 3.483.169.223 2.376 3.63 5.756 5.09 2.197.949 2.645.761 3.116.713.471-.048 1.511-.618 1.724-1.214.214-.595.214-1.106.149-1.214z"/></svg>
    </a>
</div>