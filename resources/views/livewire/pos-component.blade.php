<div x-data="{ mobileTab: 'products' }" class="flex flex-col lg:flex-row h-[calc(100vh-60px)] lg:h-[calc(100vh-100px)] gap-4 p-2 relative">
    
    <div :class="mobileTab === 'products' ? 'flex' : 'hidden lg:flex'" 
         class="w-full lg:w-2/3 flex-col h-full transition-all duration-300">
        
        <div class="bg-white p-3 rounded-xl shadow mb-3 flex gap-2 shrink-0">
            <div class="relative w-full">
                <input wire:model.live="search" type="text" placeholder="Buscar..." 
                    class="w-full pl-9 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            
            <select wire:model.live="categoriaSeleccionada" class="border rounded-lg px-2 py-2 focus:ring-2 focus:ring-blue-500 outline-none bg-white text-sm max-w-[120px] lg:max-w-none">
                <option value="">Todas</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex-1 overflow-y-auto pr-1 pb-20 lg:pb-2">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                @foreach($productos as $producto)
                <button wire:click="addToCart({{ $producto->id }})" 
                        class="bg-white p-3 rounded-xl shadow hover:shadow-lg border border-transparent active:border-blue-500 text-left group flex flex-col justify-between h-full relative overflow-hidden">
                    
                    <div class="h-24 sm:h-28 bg-gray-100 rounded-lg mb-2 flex items-center justify-center text-4xl overflow-hidden">
                        @if($producto->imagen)
                            <img src="{{ asset('storage/' . $producto->imagen) }}" class="h-full w-full object-cover">
                        @else
                            @php $cat = \Illuminate\Support\Str::lower($producto->categoria->nombre ?? ''); @endphp
                            @if(\Illuminate\Support\Str::contains($cat, ['hamburguesa', 'perro', 'comida'])) 🍔
                            @elseif(\Illuminate\Support\Str::contains($cat, ['bebida', 'gaseosa', 'jugo'])) 🥤
                            @elseif(\Illuminate\Support\Str::contains($cat, ['adicional', 'papa', 'salsa'])) 🍟
                            @elseif(\Illuminate\Support\Str::contains($cat, ['postre', 'dulce'])) 🍰
                            @else 🍽️ @endif
                        @endif
                    </div>
                    
                    <div>
                        <h3 class="font-bold text-gray-800 text-xs sm:text-sm leading-tight line-clamp-2">{{ $producto->nombre }}</h3>
                        <p class="text-blue-600 font-bold mt-1 text-base sm:text-lg">${{ number_format($producto->precio, 0) }}</p>
                    </div>

                    <div class="absolute inset-0 bg-blue-500 opacity-0 group-active:opacity-10 transition-opacity"></div>
                </button>
                @endforeach
            </div>
        </div>
    </div>

    <div :class="mobileTab === 'cart' ? 'flex' : 'hidden lg:flex'" 
         class="w-full lg:w-1/3 bg-white rounded-xl shadow-lg flex-col h-full border border-gray-200 overflow-hidden absolute inset-0 lg:static z-20 lg:z-auto">
        
        <button @click="mobileTab = 'products'" class="lg:hidden p-3 bg-gray-100 border-b flex items-center text-blue-600 font-bold">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Volver a Productos
        </button>

        <div class="flex text-xs sm:text-sm font-bold border-b shrink-0">
            <button wire:click="$set('tipo_servicio', 'Mostrador')" 
                class="flex-1 py-3 text-center transition {{ $tipo_servicio == 'Mostrador' ? 'bg-blue-600 text-white' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">
                🛍️ <span class="hidden sm:inline">Llevar</span><span class="sm:hidden">Llevar</span>
            </button>
            <button wire:click="$set('tipo_servicio', 'Mesa')" 
                class="flex-1 py-3 text-center transition {{ $tipo_servicio == 'Mesa' ? 'bg-blue-600 text-white' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">
                🍽️ <span class="hidden sm:inline">Mesa</span><span class="sm:hidden">Mesa</span>
            </button>
            <button wire:click="$set('tipo_servicio', 'Domicilio')" 
                class="flex-1 py-3 text-center transition {{ $tipo_servicio == 'Domicilio' ? 'bg-blue-600 text-white' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">
                🛵 <span class="hidden sm:inline">Domicilio</span><span class="sm:hidden">Domi</span>
            </button>
        </div>

        <div class="bg-blue-50 p-3 border-b border-blue-100 shrink-0 text-sm">
            @if($tipo_servicio == 'Mesa')
                <div class="flex items-center gap-2">
                    <label class="font-bold text-blue-800">Mesa:</label>
                    <select wire:model="numero_mesa" class="border rounded p-1 w-20 text-blue-900 font-bold">
                        <option value="">--</option>
                        @for($i=1; $i<=10; $i++) <option value="{{ $i }}">{{ $i }}</option> @endfor
                    </select>
                </div>
                @error('numero_mesa') <span class="text-red-500 text-xs block mt-1">Selecciona mesa</span> @enderror
            @endif

            @if($tipo_servicio == 'Domicilio')
                <div class="space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <input wire:model="cliente_telefono" type="tel" placeholder="📱 Teléfono" class="w-full border rounded p-1.5 focus:ring-1 focus:ring-blue-500 text-xs">
                        <input wire:model="cliente_nombre" type="text" placeholder="👤 Nombre" class="w-full border rounded p-1.5 focus:ring-1 focus:ring-blue-500 text-xs">
                    </div>
                    <input wire:model="cliente_direccion" type="text" placeholder="📍 Dirección" class="w-full border rounded p-1.5 focus:ring-1 focus:ring-blue-500 text-xs">
                    <div class="flex items-center gap-2 pt-1">
                        <label class="font-bold text-blue-800 uppercase text-xs">Envío:</label>
                        <input wire:model.live="costo_envio" type="number" class="w-20 border rounded p-1 text-right font-bold text-gray-700 text-xs">
                    </div>
                    @error('cliente_telefono') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
                    @error('cliente_nombre') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
                    @error('cliente_direccion') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
                </div>
            @endif
        </div>

        <div class="flex-1 overflow-y-auto p-3 space-y-2 bg-gray-50">
            @if(count($carrito) > 0)
                @foreach($carrito as $id => $item)
                <div class="border-b border-gray-200 pb-2 bg-white p-2 rounded shadow-sm relative" x-data="{ showNote: false }">
                    
                    <div class="flex justify-between items-center mb-1">
                        <div class="flex-1 overflow-hidden">
                            <h4 class="font-bold text-gray-700 text-sm truncate">{{ $item['nombre'] }}</h4>
                            <div class="text-xs text-gray-500">${{ number_format($item['precio'], 0) }}</div>
                        </div>
                        <div class="flex items-center gap-2 mx-2">
                            <button wire:click="decrement({{ $id }})" class="w-6 h-6 rounded bg-gray-100 text-gray-600 font-bold flex items-center justify-center">-</button>
                            <span class="font-bold text-sm w-4 text-center">{{ $item['cantidad'] }}</span>
                            <button wire:click="increment({{ $id }})" class="w-6 h-6 rounded bg-blue-100 text-blue-600 font-bold flex items-center justify-center">+</button>
                        </div>
                        <div class="text-right font-bold text-gray-700 text-sm w-16">
                            ${{ number_format($item['precio'] * $item['cantidad'], 0) }}
                        </div>
                        <button wire:click="removeItem({{ $id }})" class="text-gray-400 hover:text-red-500 ml-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="mt-1">
                        @if(!empty($item['observacion']))
                            <input type="text" 
                                   wire:model.blur="carrito.{{ $id }}.observacion" 
                                   class="w-full text-xs border-b border-gray-300 focus:border-blue-500 outline-none text-gray-600 italic bg-transparent placeholder-gray-400" 
                                   placeholder="Nota...">
                        @else
                            <button @click="showNote = !showNote" class="text-[10px] text-blue-500 hover:text-blue-700 flex items-center cursor-pointer">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Nota
                            </button>
                            
                            <div x-show="showNote" style="display: none;" class="mt-1">
                                <input type="text" 
                                       wire:model.blur="carrito.{{ $id }}.observacion" 
                                       class="w-full text-xs border rounded px-2 py-1 focus:ring-1 focus:ring-blue-500 outline-none text-gray-600" 
                                       placeholder="Ej: Sin cebolla...">
                            </div>
                        @endif
                    </div>

                </div>
                @endforeach
            @else
                <div class="h-full flex flex-col items-center justify-center text-gray-400 opacity-60">
                    <p class="text-sm font-medium">Carrito Vacío</p>
                </div>
            @endif
        </div>

        <div class="p-3 bg-white border-t shrink-0 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
             @if (session()->has('mensaje'))
                <div class="bg-green-100 text-green-700 px-2 py-1 rounded mb-2 text-xs text-center font-bold">
                    {{ session('mensaje') }}
                </div>
            @endif

            @if($tipo_servicio == 'Domicilio' && $costo_envio > 0)
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>Subtotal: ${{ number_format($total - $costo_envio, 0) }}</span>
                    <span>Envío: ${{ number_format($costo_envio, 0) }}</span>
                </div>
            @endif

            <div class="flex justify-between items-center mb-3">
                <span class="text-gray-800 font-bold">TOTAL:</span>
                <span class="text-2xl font-extrabold text-blue-800">${{ number_format($total, 0) }}</span>
            </div>

            <div class="grid grid-cols-2 gap-2 mb-2">
                <select wire:model="metodo_pago" class="border rounded p-1.5 text-sm bg-gray-50 font-medium">
                    <option value="Efectivo">💵 Efectivo</option>
                    <option value="Tarjeta">💳 Tarjeta</option>
                    <option value="Nequi/Daviplata">📱 Nequi</option>
                    <option value="Transferencia">🏦 Transf.</option>
                </select>
                <select wire:model="estado_pago" 
                    class="border rounded p-1.5 text-sm font-bold 
                    {{ $estado_pago == 'Pendiente' ? 'text-blue-600 bg-blue-50 border-blue-200' : 'text-green-600 bg-green-50 border-green-200' }}">
                    <option value="Pagado">✅ Pagado</option>
                    <option value="Pendiente">⏳ Pendiente</option>
                </select>
            </div>

            <button wire:click="cobrar" wire:loading.attr="disabled" 
                    class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 shadow-md flex justify-center items-center">
                <span wire:loading.remove>CONFIRMAR VENTA</span>
                <span wire:loading>Procesando...</span>
            </button>
        </div>
    </div>

    <div x-show="mobileTab === 'products'" 
         class="lg:hidden fixed bottom-4 left-4 right-4 z-30">
        <button @click="mobileTab = 'cart'" 
                class="w-full bg-slate-900 text-white shadow-xl rounded-xl p-4 flex justify-between items-center transition transform active:scale-95 border border-slate-700">
            <div class="flex items-center">
                <span class="bg-yellow-400 text-slate-900 font-bold w-8 h-8 rounded-full flex items-center justify-center mr-3">
                    {{ $articulosCount }}
                </span>
                <span class="font-bold text-lg">Ver Pedido</span>
            </div>
            <span class="font-bold text-xl text-yellow-400">${{ number_format($total, 0) }}</span>
        </button>
    </div>

</div>