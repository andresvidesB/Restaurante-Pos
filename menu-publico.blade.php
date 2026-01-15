<div class="min-h-screen bg-[#1a1a1a] font-sans text-gray-200" style="background-image: url('https://www.transparenttextures.com/patterns/dark-matter.png');">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <style>
        /* Animación de partículas */
        @keyframes fly {
            0% { transform: translateY(100%) translateX(0); opacity: 1; }
            50% { opacity: 0.8; }
            100% { transform: translateY(-100%) translateX(50px); opacity: 0; }
        }
        .spark { position: absolute; width: 3px; height: 3px; background: #ff7b00; border-radius: 50%; filter: blur(1px); animation: fly 6s infinite ease-in; opacity: 0; pointer-events: none; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #ea580c; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #333; }
        .slide-in-right { animation: slideIn 0.3s ease-out forwards; }
        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
    </style>

    <nav x-data="{ open: false }" class="fixed top-0 w-full z-50 transition-all duration-300 bg-black/30 backdrop-blur-xl border-b border-white/10 shadow-[0_4px_30px_rgba(0,0,0,0.1)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                
                <div class="flex-shrink-0 flex items-center cursor-pointer group" wire:click="filtrarCategoria(0)">
                    
                    <span class="text-2xl font-black text-white tracking-tighter drop-shadow-md">
                        Gourmet<span class="text-orange-500 italic">CARBÓN</span>
                    </span>
                </div>

                <div class="hidden md:flex items-center gap-6">
                    @auth
                        <div class="flex items-center gap-4 bg-white/5 px-4 py-1.5 rounded-full border border-white/10 backdrop-blur-md">
                            <span class="text-xs font-bold text-gray-300">Hola, {{ auth()->user()->name }}</span>
                            <div class="h-4 w-px bg-white/20"></div>
                            <a href="{{ route('mis-pedidos') }}" class="text-xs font-bold text-orange-400 hover:text-orange-300 transition">
                                Mis Pedidos
                            </a>
                        </div>
                        <button wire:click="logout" class="text-xs font-bold text-white hover:text-red-400 transition">Salir</button>
                    @else
                        <div class="flex items-center gap-3">
                            <a href="{{ route('login') }}" class="text-sm font-bold text-white hover:text-orange-400 transition px-4 py-2 rounded-lg hover:bg-white/5 border border-transparent hover:border-white/10">
                                Iniciar Sesión
                            </a>
                            <a href="{{ route('register') }}" class="text-sm font-bold text-white bg-orange-600 hover:bg-orange-700 px-5 py-2 rounded-lg shadow-lg shadow-orange-900/20 transition transform hover:scale-105">
                                Registrarse
                            </a>
                        </div>
                    @endauth
                    
                    <button wire:click="toggleCarrito" class="relative p-2.5 bg-white/5 rounded-full hover:bg-white/10 border border-white/5 transition group">
                        <svg class="w-6 h-6 text-gray-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        @if(count($carrito) > 0)
                            <span class="absolute -top-1 -right-1 bg-orange-600 text-white text-[10px] font-black w-5 h-5 flex items-center justify-center rounded-full border-2 border-[#121212]">{{ collect($carrito)->sum('cantidad') }}</span>
                        @endif
                    </button>
                </div>

                <div class="flex items-center gap-4 md:hidden">
                    <button wire:click="toggleCarrito" class="relative p-2 text-white hover:text-orange-500 transition">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        @if(count($carrito) > 0)
                            <span class="absolute top-0 right-0 bg-orange-600 text-white text-[9px] font-black w-4 h-4 flex items-center justify-center rounded-full">{{ collect($carrito)->sum('cantidad') }}</span>
                        @endif
                    </button>

                    <button @click="open = ! open" class="text-white hover:text-orange-500 focus:outline-none bg-white/5 p-2 rounded-lg backdrop-blur-sm border border-white/10">
                        <svg class="h-7 w-7" stroke="currentColor" fill="none" viewBox="0 0 24 24" x-show="!open">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="h-7 w-7" stroke="currentColor" fill="none" viewBox="0 0 24 24" x-show="open" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="open" style="display: none;" 
             class="md:hidden absolute top-20 left-0 w-full bg-[#121212]/95 backdrop-blur-xl border-b border-white/10 shadow-2xl transition-all">
            <div class="px-6 py-6 space-y-4">
                @auth
                    <div class="flex items-center gap-3 px-4 py-4 bg-white/5 rounded-xl border border-white/5">
                        <div class="bg-gradient-to-br from-orange-600 to-red-600 w-10 h-10 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white">{{ auth()->user()->name }}</p>
                            <a href="{{ route('mis-pedidos') }}" class="text-xs text-orange-400 hover:text-orange-300 font-medium">Ver mis pedidos →</a>
                        </div>
                    </div>
                    <button wire:click="logout" class="block w-full text-center px-4 py-3 text-red-400 font-bold border border-red-500/20 rounded-xl hover:bg-red-500/10 transition">
                        Cerrar Sesión
                    </button>
                @else
                    <div class="grid grid-cols-1 gap-3">
                        <a href="{{ route('login') }}" class="block w-full text-center px-4 py-3.5 bg-white/5 text-white rounded-xl font-bold border border-white/10 hover:bg-white/10 transition">
                            Iniciar Sesión
                        </a>
                        <a href="{{ route('register') }}" class="block w-full text-center px-4 py-3.5 bg-orange-600 text-white rounded-xl font-black uppercase tracking-wider hover:bg-orange-500 shadow-lg shadow-orange-900/30 transition">
                            Registrarse
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <div class="pt-20"></div>

    @if($categoriaSeleccionada == 0)
    <div class="relative h-[50vh] md:h-[60vh] flex items-center justify-center bg-black overflow-hidden border-b border-white/5">
        <div class="absolute inset-0 z-10 overflow-hidden pointer-events-none">
            @for($i=0; $i<20; $i++)
                <div class="spark" style="left:{{rand(0,100)}}%; animation-delay:{{rand(0,5)}}s;"></div>
            @endfor
        </div>
        <div class="absolute inset-0 opacity-40">
            <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?q=80&w=1974&auto=format&fit=crop" class="w-full h-full object-cover">
        </div>
        <div class="absolute inset-0 bg-gradient-to-b from-[#1a1a1a]/10 via-transparent to-[#1a1a1a]"></div>
        
        <div class="relative z-20 text-center px-4">
            <h1 class="text-4xl md:text-7xl font-black text-white uppercase italic drop-shadow-2xl tracking-tight">
                Directo al <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-red-600">Carbón</span>
            </h1>
            <p class="mt-2 md:mt-4 text-lg text-gray-300 font-light italic">Sabor ahumado y cortes premium en cada bocado.</p>
        </div>
    </div>
    @endif

    <div class="sticky top-20 bg-[#1a1a1a]/90 backdrop-blur-md z-30 py-4 border-b border-white/5 shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex overflow-x-auto gap-3 pb-2 no-scrollbar">
                <button wire:click="filtrarCategoria(0)" class="whitespace-nowrap px-6 py-2 rounded-full text-xs font-black uppercase transition-all duration-300 {{ $categoriaSeleccionada == 0 ? 'bg-orange-600 text-white shadow-lg shadow-orange-900/50 scale-105' : 'bg-[#222] text-gray-400 border border-white/10 hover:border-white/30 hover:text-white' }}">🔥 Todo</button>
                @foreach($categorias as $cat)
                    <button wire:click="filtrarCategoria({{ $cat->id }})" class="whitespace-nowrap px-6 py-2 rounded-full text-xs font-black uppercase transition-all duration-300 {{ $categoriaSeleccionada == $cat->id ? 'bg-orange-600 text-white shadow-lg shadow-orange-900/50 scale-105' : 'bg-[#222] text-gray-400 border border-white/10 hover:border-white/30 hover:text-white' }}">{{ $cat->nombre }}</button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8 pb-32">
        
        @if(count($ofertas) > 0 && $categoriaSeleccionada == 0)
            <h2 class="text-xl font-black text-white mb-6 uppercase italic flex items-center gap-2"><span class="text-orange-500 text-2xl animate-pulse">🔥</span> Recomendados</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                @foreach($ofertas as $oferta)
                    <div class="bg-[#222] rounded-2xl border border-white/5 overflow-hidden flex group hover:border-orange-500/50 transition relative shadow-xl">
                        <div class="w-1/3 bg-gray-800 relative">
                            @if($oferta->imagen) <img src="{{ asset('storage/'.$oferta->imagen) }}" class="w-full h-full object-cover"> @endif
                            <div class="absolute inset-0 bg-black/20 group-hover:bg-transparent transition"></div>
                        </div>
                        <div class="p-4 w-2/3 flex flex-col justify-between bg-gradient-to-br from-[#222] to-[#1a1a1a]">
                            <div>
                                <h3 class="font-bold text-white leading-tight group-hover:text-orange-400 transition">{{ $oferta->nombre }}</h3>
                                <div class="mt-2 flex gap-2 items-baseline">
                                    <span class="text-xl font-black text-orange-500">${{ number_format($oferta->precio_oferta, 0) }}</span>
                                    <span class="text-xs text-gray-500 line-through">${{ number_format($oferta->precio, 0) }}</span>
                                </div>
                            </div>
                            <button wire:click="agregarAlCarrito({{ $oferta->id }})" class="mt-2 w-full bg-white/10 border border-white/10 text-white text-xs font-bold py-2 rounded-lg hover:bg-orange-600 hover:border-orange-600 transition uppercase tracking-wider">AGREGAR</button>
                        </div>
                        <div class="absolute top-0 right-0 bg-orange-600 text-white text-[9px] font-black px-2 py-1 rounded-bl-lg shadow-md">OFERTA</div>
                    </div>
                @endforeach
            </div>
        @endif

        <h2 class="text-xl font-black text-white mb-6 uppercase italic flex items-center gap-2">La Carta</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($productos as $producto)
                <div class="bg-[#222] rounded-3xl border border-white/5 overflow-hidden flex flex-col shadow-lg hover:shadow-orange-900/10 hover:border-orange-500/30 transition duration-300 group">
                    <div class="h-48 bg-neutral-900 overflow-hidden relative">
                        @if($producto->imagen)
                            <img src="{{ asset('storage/'.$producto->imagen) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700 ease-in-out">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-60"></div>
                        
                        @if($producto->es_oferta)
                            <span class="absolute top-3 right-3 bg-red-600 text-white text-[10px] font-black px-2 py-1 rounded shadow-lg">SALE</span>
                        @endif
                    </div>
                    <div class="p-5 flex-1 flex flex-col justify-between bg-[#222]">
                        <div>
                            <p class="text-[10px] text-orange-500 font-black uppercase tracking-wider mb-1">{{ $producto->categoria->nombre ?? 'GRILL' }}</p>
                            <h3 class="text-lg font-bold text-white leading-tight mb-2 group-hover:text-orange-400 transition">{{ $producto->nombre }}</h3>
                        </div>
                        <div class="mt-4 flex items-center justify-between border-t border-white/5 pt-3">
                            <span class="text-xl font-black text-white">
                                ${{ number_format($producto->es_oferta && $producto->precio_oferta ? $producto->precio_oferta : $producto->precio, 0) }}
                            </span>
                            <button wire:click="agregarAlCarrito({{ $producto->id }})" class="w-10 h-10 rounded-full bg-white text-black hover:bg-orange-600 hover:text-white flex items-center justify-center transition shadow-lg hover:scale-110">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- CARRITO FLOTANTE (ESTILO ORIGINAL MANTENIDO) --}}
    @if(!$mostrarCarrito && count($carrito) > 0)
        <button wire:click="toggleCarrito" class="fixed bottom-6 right-6 z-50 bg-orange-600 text-white w-16 h-16 rounded-full shadow-[0_0_20px_rgba(234,88,12,0.6)] flex items-center justify-center transition transform hover:scale-110 animate-bounce">
            <div class="relative">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                <span class="absolute -top-2 -right-2 bg-white text-orange-600 text-xs font-black w-5 h-5 flex items-center justify-center rounded-full border-2 border-orange-600">{{ collect($carrito)->sum('cantidad') }}</span>
            </div>
        </button>
    @endif

    @if($mostrarCarrito)
    <div class="fixed inset-0 z-50 flex justify-end">
        <div wire:click="toggleCarrito" class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>
        <div class="relative w-full max-w-md h-full bg-[#1a1a1a] shadow-2xl flex flex-col border-l border-white/10 slide-in-right">
            <div class="p-6 border-b border-white/5 bg-[#161616] flex justify-between items-center">
                <h2 class="text-xl font-black text-white uppercase italic flex items-center gap-2"><span class="text-orange-500">🔥</span> Tu Pedido</h2>
                <button wire:click="toggleCarrito" class="text-gray-500 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
                @if(count($carrito) == 0)
                    <div class="h-full flex flex-col items-center justify-center text-gray-600 opacity-50">
                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <p class="font-bold uppercase tracking-widest text-sm">Tu parrilla está vacía</p>
                    </div>
                @else
                    @if($pasoCheckout == 1)
                        <div class="space-y-4">
                            @foreach($carrito as $item)
                                <div class="flex gap-4 items-center bg-[#222] p-3 rounded-xl border border-white/5">
                                    <div class="w-16 h-16 bg-gray-800 rounded-lg overflow-hidden flex-shrink-0">
                                        @if($item['imagen']) <img src="{{ asset('storage/'.$item['imagen']) }}" class="w-full h-full object-cover"> @endif
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-white font-bold text-sm leading-tight mb-1">{{ $item['nombre'] }}</h4>
                                        <p class="text-orange-500 font-black text-sm">${{ number_format($item['precio'] * $item['cantidad'], 0) }}</p>
                                    </div>
                                    <div class="flex flex-col items-center gap-1 bg-[#161616] rounded-lg p-1 border border-white/5">
                                        <button wire:click="incrementarCantidad({{ $item['id'] }})" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 rounded transition">+</button>
                                        <span class="text-xs font-bold text-white">{{ $item['cantidad'] }}</span>
                                        <button wire:click="decrementarCantidad({{ $item['id'] }})" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 rounded transition">-</button>
                                    </div>
                                    <button wire:click="eliminarDelCarrito({{ $item['id'] }})" class="text-gray-600 hover:text-red-500 p-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @if($pasoCheckout == 2)
                        <div class="space-y-6">
                            <div class="bg-[#222] p-2 rounded-xl border border-white/5">
                                <h3 class="text-gray-500 font-bold text-[10px] uppercase mb-2 ml-1">Método de Entrega</h3>
                                <div class="flex bg-black/40 p-1 rounded-lg border border-white/5">
                                    <button wire:click="$set('tipoEntrega', 'recogida')" class="flex-1 py-2 rounded-md text-[10px] font-black uppercase transition-all {{ $tipoEntrega == 'recogida' ? 'bg-orange-600 text-white shadow-lg' : 'text-gray-500 hover:text-gray-300' }}">Recogida</button>
                                    <button wire:click="$set('tipoEntrega', 'domicilio')" class="flex-1 py-2 rounded-md text-[10px] font-black uppercase transition-all {{ $tipoEntrega == 'domicilio' ? 'bg-orange-600 text-white shadow-lg' : 'text-gray-500 hover:text-gray-300' }}">Domicilio</button>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <h3 class="text-white font-bold text-sm uppercase border-b border-white/5 pb-2">Tus Datos</h3>
                                <div class="grid grid-cols-1 gap-3">
                                    <div><label class="block text-[10px] uppercase font-bold text-gray-500 mb-1">Nombre</label><input wire:model="nombre_cliente" type="text" class="w-full bg-[#222] border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:border-orange-500 outline-none" placeholder="Nombre"></div>
                                    <div><label class="block text-[10px] uppercase font-bold text-gray-500 mb-1">Teléfono</label><input wire:model="telefono_cliente" type="tel" class="w-full bg-[#222] border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:border-orange-500 outline-none" placeholder="Teléfono"></div>
                                </div>
                                <div><label class="block text-[10px] uppercase font-bold text-orange-500 mb-1">📝 Nota</label><textarea wire:model="nota_cliente" rows="2" class="w-full bg-[#222] border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:border-orange-500 outline-none resize-none"></textarea></div>
                                @if($tipoEntrega == 'domicilio')
                                    <div class="animate-fadeIn p-3 bg-black/20 rounded-xl border border-white/5 space-y-3">
                                        <div><label class="block text-[10px] uppercase font-bold text-gray-500 mb-1">Barrio</label><input wire:model="barrio_cliente" type="text" class="w-full bg-[#222] border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:border-orange-500 outline-none"></div>
                                        <div><label class="block text-[10px] uppercase font-bold text-gray-500 mb-1">Dirección</label><input wire:model="direccion_cliente" type="text" class="w-full bg-[#222] border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:border-orange-500 outline-none"></div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif
            </div>
            @if(count($carrito) > 0)
                <div class="p-6 bg-[#161616] border-t border-white/5">
                    @if($pasoCheckout == 1)
                        <div class="flex justify-between items-end mb-4"><span class="text-gray-400 text-xs font-bold uppercase tracking-widest">Total</span><span class="text-2xl font-black text-white">${{ number_format($this->subtotal, 0) }}</span></div>
                        <button wire:click="irAlPaso2" class="w-full bg-white text-black font-black uppercase tracking-widest py-3 rounded-xl hover:bg-orange-600 hover:text-white transition duration-300 shadow-xl flex items-center justify-center gap-2">Continuar</button>
                    @else
                        <div class="space-y-4">
                            <div><label class="block text-[10px] uppercase font-bold text-gray-500 mb-2">Pago</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <button wire:click="$set('metodo_pago', 'efectivo')" class="flex items-center justify-center gap-2 py-2.5 px-2 rounded-xl border transition-all {{ $metodo_pago == 'efectivo' ? 'bg-green-600/20 border-green-500 text-green-400' : 'bg-[#222] border-white/10 text-gray-400 hover:bg-[#2a2a2a]' }}">💵 Efec.</button>
                                    <button wire:click="$set('metodo_pago', 'transferencia')" class="flex items-center justify-center gap-2 py-2.5 px-2 rounded-xl border transition-all {{ $metodo_pago == 'transferencia' ? 'bg-purple-600/20 border-purple-500 text-purple-400' : 'bg-[#222] border-white/10 text-gray-400 hover:bg-[#2a2a2a]' }}">📱 Transf.</button>
                                </div>
                            </div>
                            <div class="flex gap-2 pt-2 border-t border-white/10">
                                <button wire:click="volverAlPaso1" class="px-4 bg-[#222] text-white font-bold uppercase py-3 rounded-xl border border-white/10">←</button>
                                <button wire:click="finalizarPedido" class="flex-1 bg-green-600 text-white font-black uppercase tracking-widest py-3 rounded-xl hover:bg-green-500 transition shadow-xl">Pedir ${{ number_format($this->subtotal, 0) }}</button>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
    @endif

    <footer class="bg-black text-gray-500 py-8 text-center text-xs border-t border-white/5">
        <p>&copy; 2026 GOURMET CARBÓN. TODOS LOS DERECHOS RESERVADOS.</p>
    </footer>
</div>