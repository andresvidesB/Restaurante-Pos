<div class="min-h-screen bg-[#1a1a1a] font-sans text-gray-200" style="background-image: url('https://www.transparenttextures.com/patterns/dark-matter.png');">
    
    <style>
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
        
        /* Animación de entrada del carrito */
        .slide-in-right { animation: slideIn 0.3s ease-out forwards; }
        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
    </style>

    <nav class="bg-[#121212]/90 backdrop-blur-md border-b border-white/10 shadow-2xl sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center cursor-pointer" wire:click="filtrarCategoria(0)">
                    <span class="text-2xl font-black text-white tracking-tighter">
                        Gourmet<span class="text-orange-500 italic">CARBÓN</span>
                    </span>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="flex items-center gap-4">
                            <span class="text-xs font-bold text-gray-400 hidden sm:block">{{ auth()->user()->name }}</span>
                            <button wire:click="logout" class="text-xs font-medium text-orange-500 hover:text-white border border-orange-500/30 px-3 py-1 rounded-full transition">Salir</button>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="hidden md:block text-xs font-medium text-gray-400 hover:text-white">Ingresar</a>
                    @endauth
                    
                    <button wire:click="toggleCarrito" class="relative p-2 text-white hover:text-orange-500 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        @if(count($carrito) > 0)
                            <span class="absolute top-0 right-0 bg-orange-600 text-white text-[10px] font-black w-4 h-4 flex items-center justify-center rounded-full animate-bounce">{{ collect($carrito)->sum('cantidad') }}</span>
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </nav>

    @if($categoriaSeleccionada == 0)
    <div class="relative h-[50vh] md:h-[60vh] flex items-center justify-center bg-black overflow-hidden">
        <div class="absolute inset-0 z-10 overflow-hidden pointer-events-none">
            @for($i=0; $i<20; $i++)
                <div class="spark" style="left:{{rand(0,100)}}%; animation-delay:{{rand(0,5)}}s;"></div>
            @endfor
        </div>
        <div class="absolute inset-0 opacity-40">
            <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?q=80&w=1974&auto=format&fit=crop" class="w-full h-full object-cover">
        </div>
        <div class="relative z-20 text-center px-4">
            <h1 class="text-4xl md:text-7xl font-black text-white uppercase italic drop-shadow-2xl">Directo al <span class="text-orange-600">Carbón</span></h1>
            <p class="mt-2 md:mt-4 text-lg text-gray-300 font-light italic">Sabor ahumado en cada bocado.</p>
        </div>
        <div class="absolute bottom-0 w-full h-32 bg-gradient-to-t from-[#1a1a1a] to-transparent"></div>
    </div>
    @endif

    <div class="sticky top-16 bg-[#1a1a1a]/95 backdrop-blur z-30 py-4 border-b border-white/5">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex overflow-x-auto gap-3 pb-2 no-scrollbar">
                <button wire:click="filtrarCategoria(0)" class="whitespace-nowrap px-6 py-2 rounded-full text-xs font-black uppercase {{ $categoriaSeleccionada == 0 ? 'bg-orange-600 text-white shadow-lg' : 'bg-[#222] text-gray-400 border border-white/10' }}">🔥 Todo</button>
                @foreach($categorias as $cat)
                    <button wire:click="filtrarCategoria({{ $cat->id }})" class="whitespace-nowrap px-6 py-2 rounded-full text-xs font-black uppercase {{ $categoriaSeleccionada == $cat->id ? 'bg-orange-600 text-white' : 'bg-[#222] text-gray-400 border border-white/10' }}">{{ $cat->nombre }}</button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8 pb-32">
        @if(count($ofertas) > 0 && $categoriaSeleccionada == 0)
            <h2 class="text-xl font-black text-white mb-6 uppercase italic flex items-center gap-2"><span class="text-orange-500 text-2xl">🔥</span> Recomendados</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                @foreach($ofertas as $oferta)
                    <div class="bg-[#222] rounded-2xl border border-orange-500/20 overflow-hidden flex group hover:border-orange-500 transition relative">
                        <div class="w-1/3 bg-gray-800">
                            @if($oferta->imagen) <img src="{{ asset('storage/'.$oferta->imagen) }}" class="w-full h-full object-cover"> @endif
                        </div>
                        <div class="p-4 w-2/3 flex flex-col justify-between">
                            <div>
                                <h3 class="font-bold text-white leading-tight">{{ $oferta->nombre }}</h3>
                                <div class="mt-2 flex gap-2 items-baseline">
                                    <span class="text-xl font-black text-orange-500">${{ number_format($oferta->precio_oferta, 0) }}</span>
                                    <span class="text-xs text-gray-500 line-through">${{ number_format($oferta->precio, 0) }}</span>
                                </div>
                            </div>
                            <button wire:click="agregarAlCarrito({{ $oferta->id }})" class="mt-2 w-full bg-orange-600 text-white text-xs font-bold py-2 rounded-lg hover:bg-orange-500 transition">AGREGAR</button>
                        </div>
                        <div class="absolute top-0 right-0 bg-orange-600 text-white text-[9px] font-black px-2 py-1 rounded-bl-lg">OFERTA</div>
                    </div>
                @endforeach
            </div>
        @endif

        <h2 class="text-xl font-black text-white mb-6 uppercase italic">La Carta</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($productos as $producto)
                <div class="bg-[#222] rounded-3xl border border-white/5 overflow-hidden flex flex-col shadow-lg hover:border-orange-500/30 transition duration-300">
                    <div class="h-48 bg-neutral-900 overflow-hidden relative">
                        @if($producto->imagen)
                            <img src="{{ asset('storage/'.$producto->imagen) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @endif
                        @if($producto->es_oferta)
                            <span class="absolute top-3 right-3 bg-orange-600 text-white text-[10px] font-black px-2 py-1 rounded shadow-lg">SALE</span>
                        @endif
                    </div>
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <p class="text-[10px] text-orange-500 font-black uppercase tracking-wider mb-1">{{ $producto->categoria->nombre ?? 'GRILL' }}</p>
                            <h3 class="text-lg font-bold text-white leading-tight mb-2">{{ $producto->nombre }}</h3>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-xl font-black text-white">
                                ${{ number_format($producto->es_oferta && $producto->precio_oferta ? $producto->precio_oferta : $producto->precio, 0) }}
                            </span>
                            <button wire:click="agregarAlCarrito({{ $producto->id }})" class="w-10 h-10 rounded-xl bg-white text-black hover:bg-orange-600 hover:text-white flex items-center justify-center transition shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

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
                <h2 class="text-xl font-black text-white uppercase italic flex items-center gap-2">
                    <span class="text-orange-500">🔥</span> Tu Pedido
                </h2>
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
                            <div class="bg-orange-900/20 border border-orange-500/30 p-4 rounded-xl">
                                <h3 class="text-orange-500 font-black text-sm uppercase mb-2">Método de Entrega</h3>
                                <div class="flex gap-2">
                                    <button wire:click="$set('tipoEntrega', 'recogida')" class="flex-1 py-2 rounded-lg text-xs font-bold uppercase border transition {{ $tipoEntrega == 'recogida' ? 'bg-orange-600 text-white border-orange-600' : 'bg-transparent text-gray-400 border-white/10 hover:border-white/30' }}">
                                        Recogida
                                    </button>
                                    <button wire:click="$set('tipoEntrega', 'domicilio')" class="flex-1 py-2 rounded-lg text-xs font-bold uppercase border transition {{ $tipoEntrega == 'domicilio' ? 'bg-orange-600 text-white border-orange-600' : 'bg-transparent text-gray-400 border-white/10 hover:border-white/30' }}">
                                        Domicilio
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h3 class="text-white font-bold text-sm uppercase">Tus Datos</h3>
                                
                                <div>
                                    <label class="block text-[10px] uppercase font-bold text-gray-500 mb-1 ml-1">Nombre Completo</label>
                                    <input wire:model="nombre_cliente" type="text" class="w-full bg-[#222] border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none" placeholder="Ej: Juan Pérez">
                                    @error('nombre_cliente') <span class="text-red-500 text-[10px] ml-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-[10px] uppercase font-bold text-gray-500 mb-1 ml-1">Teléfono / WhatsApp</label>
                                    <input wire:model="telefono_cliente" type="tel" class="w-full bg-[#222] border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none" placeholder="Ej: 300 123 4567">
                                    @error('telefono_cliente') <span class="text-red-500 text-[10px] ml-1">{{ $message }}</span> @enderror
                                </div>

                                @if($tipoEntrega == 'domicilio')
                                    <div class="animate-fadeIn">
                                        <div class="mb-4">
                                            <label class="block text-[10px] uppercase font-bold text-gray-500 mb-1 ml-1">Barrio</label>
                                            <input wire:model="barrio_cliente" type="text" class="w-full bg-[#222] border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none" placeholder="Ej: Centro">
                                            @error('barrio_cliente') <span class="text-red-500 text-[10px] ml-1">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-[10px] uppercase font-bold text-gray-500 mb-1 ml-1">Dirección Exacta</label>
                                            <input wire:model="direccion_cliente" type="text" class="w-full bg-[#222] border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none" placeholder="Ej: Calle 10 # 5-20">
                                            @error('direccion_cliente') <span class="text-red-500 text-[10px] ml-1">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div class="mt-4 p-3 bg-blue-900/20 border border-blue-500/20 rounded-xl flex gap-3 items-start">
                                            <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p class="text-[10px] text-blue-200 leading-relaxed">
                                                <strong>Importante:</strong> El costo del domicilio NO está incluido en el total. Te lo informaremos al confirmar tu pedido por WhatsApp.
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                @endif
            </div>

            @if(count($carrito) > 0)
                <div class="p-6 bg-[#161616] border-t border-white/5">
                    <div class="flex justify-between items-end mb-4">
                        <span class="text-gray-400 text-xs font-bold uppercase tracking-widest">Total Productos</span>
                        <span class="text-2xl font-black text-white">${{ number_format($this->subtotal, 0) }}</span>
                    </div>

                    @if($pasoCheckout == 1)
                        <button wire:click="irAlPaso2" class="w-full bg-white text-black font-black uppercase tracking-widest py-4 rounded-xl hover:bg-orange-600 hover:text-white transition duration-300 shadow-xl flex items-center justify-center gap-2">
                            Continuar
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    @else
                        <div class="flex gap-3">
                            <button wire:click="volverAlPaso1" class="w-1/3 bg-[#222] text-white font-bold uppercase tracking-widest py-4 rounded-xl hover:bg-[#333] transition border border-white/10">
                                Volver
                            </button>
                            <button wire:click="finalizarPedido" class="w-2/3 bg-green-600 text-white font-black uppercase tracking-widest py-4 rounded-xl hover:bg-green-500 transition shadow-xl flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-8.683-2.031-9.676-.272-.099-.47-.149-.669-.149-.198 0-.42.001-.643.001-.223 0-.583.084-.89.421-.307.337-1.178 1.151-1.178 2.809 0 1.658 1.208 3.26 1.376 3.483.169.223 2.376 3.63 5.756 5.09 2.197.949 2.645.761 3.116.713.471-.048 1.511-.618 1.724-1.214.214-.595.214-1.106.149-1.214z"/></svg>
                                Pedir
                            </button>
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