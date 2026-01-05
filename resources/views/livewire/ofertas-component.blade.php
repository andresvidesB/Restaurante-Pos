<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-black text-gray-800">🔥 Gestión de Ofertas</h2>
            <p class="text-gray-500 text-sm">Administra los precios especiales de tu menú.</p>
        </div>
        <button wire:click="create" class="bg-black text-white px-6 py-3 rounded-full font-bold shadow-lg hover:scale-105 transition flex items-center gap-2">
            <span>+</span> Nueva Oferta
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm animate-pulse">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($ofertas as $oferta)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden relative group transition hover:shadow-xl">
                
                <div class="h-32 bg-gray-200 overflow-hidden relative">
                    @if($oferta->imagen)
                        <img src="{{ asset('storage/'.$oferta->imagen) }}" class="w-full h-full object-cover opacity-90 group-hover:scale-110 transition duration-500">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                    
                    <div class="absolute bottom-3 left-4 right-4">
                        <h3 class="text-white font-bold text-xl leading-tight drop-shadow-md truncate">{{ $oferta->nombre }}</h3>
                        <p class="text-gray-300 text-xs uppercase tracking-wider">{{ $oferta->categoria->nombre ?? 'General' }}</p>
                    </div>
                </div>

                <div class="p-5">
                    <div class="flex items-center justify-between mb-4 bg-gray-50 p-3 rounded-xl">
                        <div class="text-center w-1/2 border-r border-gray-200">
                            <span class="block text-xs text-gray-400 uppercase font-bold">Real</span>
                            <span class="text-gray-500 line-through font-medium">${{ number_format($oferta->precio, 0) }}</span>
                        </div>
                        <div class="text-center w-1/2">
                            <span class="block text-xs text-red-500 uppercase font-bold">Oferta</span>
                            @if($oferta->precio_oferta > 0)
                                <span class="text-2xl font-black text-red-600">${{ number_format($oferta->precio_oferta, 0) }}</span>
                            @else
                                <span class="text-lg font-bold text-yellow-600 bg-yellow-100 px-2 py-1 rounded">¡Sin Precio!</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button wire:click="edit({{ $oferta->id }})" class="flex-1 bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700 transition flex items-center justify-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Editar Precio
                        </button>
                        
                        <button wire:click="quitarOferta({{ $oferta->id }})" class="w-10 bg-red-50 text-red-500 border border-red-100 rounded-lg hover:bg-red-100 transition flex items-center justify-center" title="Quitar Oferta">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 flex flex-col items-center justify-center text-gray-400 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path></svg>
                <p class="text-lg font-medium">No hay ofertas activas actualmente.</p>
                <button wire:click="create" class="mt-4 text-blue-600 hover:underline">¡Crea la primera!</button>
            </div>
        @endforelse
    </div>

    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 transition-opacity">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all scale-100">
                
                <div class="p-5 border-b flex justify-between items-center bg-gray-50">
                    <div>
                        <h3 class="text-xl font-black text-gray-900">
                            {{ $producto_seleccionado_id ? 'Definir Precio de Oferta' : 'Buscar Producto' }}
                        </h3>
                    </div>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6">
                    @if(!$producto_seleccionado_id)
                        <div class="space-y-4">
                            <label class="block text-sm font-bold text-gray-700">🔍 Buscar producto para poner en oferta:</label>
                            <input wire:model.live="searchProduct" type="text" placeholder="Ej. Hamburguesa doble..." class="w-full border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-black focus:border-black shadow-sm transition">
                            
                            <div class="max-h-60 overflow-y-auto border rounded-xl divide-y">
                                @forelse($productosDisponibles as $prod)
                                    <button wire:click="seleccionarProducto({{ $prod->id }})" class="w-full text-left px-4 py-3 hover:bg-blue-50 flex justify-between items-center transition group">
                                        <div>
                                            <span class="font-bold text-gray-800 block group-hover:text-blue-700">{{ $prod->nombre }}</span>
                                            <span class="text-xs text-gray-400">{{ $prod->categoria->nombre ?? 'General' }}</span>
                                        </div>
                                        <span class="font-bold text-gray-600">${{ number_format($prod->precio, 0) }}</span>
                                    </button>
                                @empty
                                    @if(strlen($searchProduct) > 0)
                                        <div class="p-4 text-center text-gray-400 text-sm">No se encontraron productos disponibles.</div>
                                    @else
                                        <div class="p-4 text-center text-gray-400 text-xs italic">Escribe para buscar...</div>
                                    @endif
                                @endforelse
                            </div>
                        </div>

                    @else
                        <div class="space-y-6">
                            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wide">Producto</span>
                                    <p class="font-bold text-gray-900 text-lg leading-tight">{{ $nombre_producto }}</p>
                                </div>
                                <button wire:click="$set('producto_seleccionado_id', null)" class="text-xs font-bold text-blue-500 hover:text-blue-700 underline">
                                    Cambiar
                                </button>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Precio Actual</label>
                                    <div class="w-full bg-gray-100 border border-gray-200 rounded-xl p-3 text-gray-500 font-bold select-none">
                                        ${{ number_format($precio_original, 0) }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-red-500 uppercase mb-1">Nuevo Precio (Oferta)</label>
                                    <input wire:model="precio_oferta" type="number" class="w-full border-2 border-blue-100 rounded-xl p-3 font-bold text-gray-900 focus:ring-0 focus:border-blue-500 outline-none shadow-sm text-lg placeholder-gray-300" placeholder="Ej: 15000">
                                    @error('precio_oferta') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="p-5 border-t bg-gray-50 flex justify-end gap-3">
                    <button wire:click="closeModal" class="px-5 py-2.5 rounded-xl text-gray-600 font-bold hover:bg-gray-200 transition">Cancelar</button>
                    @if($producto_seleccionado_id)
                        <button wire:click="store" class="px-6 py-2.5 rounded-xl bg-black text-white font-bold hover:bg-gray-800 transition shadow-lg transform active:scale-95">
                            Guardar Oferta
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>