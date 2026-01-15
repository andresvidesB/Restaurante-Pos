@php
    $cajaAbierta = \App\Models\Caja::where('user_id', auth()->id())->whereNull('fecha_cierre')->exists();
@endphp

<div class="h-[calc(100vh-65px)] bg-gray-100 overflow-hidden flex flex-col font-sans" x-data="{ mobileTab: 'products' }">

    @if(!$cajaAbierta)
        <div class="bg-red-600 text-white text-xs font-bold text-center py-1 z-50">
            🔒 CAJA CERRADA. <a href="{{ route('cajas.index') }}" class="underline hover:text-gray-200">ABRIR AHORA</a>
        </div>
    @endif

    <div class="flex-1 flex overflow-hidden">
        
        <div :class="mobileTab === 'products' ? 'flex' : 'hidden lg:flex'" 
             class="w-full lg:w-[70%] flex-col h-full border-r border-gray-300">
            
            <div class="bg-white p-2 border-b border-gray-300 flex gap-2 shrink-0 h-14 items-center shadow-sm z-10">
                <div class="relative flex-1">
                    <span class="absolute left-3 top-2 text-gray-400">🔍</span>
                    <input wire:model.live="search" type="text" placeholder="Buscar producto..." 
                        class="w-full pl-8 pr-2 py-1.5 border border-gray-300 rounded bg-gray-50 text-sm focus:ring-1 focus:ring-gray-800 outline-none placeholder-gray-400 font-medium">
                </div>
                
                <div class="w-[180px]">
                    <select wire:model.live="categoriaSeleccionada" 
                        class="w-full border border-gray-300 rounded bg-gray-50 text-xs font-bold h-9 px-2 outline-none focus:border-gray-800 cursor-pointer uppercase text-gray-700">
                        <option value="">📂 TODAS LAS CATEGORÍAS</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-2 bg-gray-200/50 content-start custom-scrollbar">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-2">
                    @foreach($productos as $producto)
                        @php 
                            $enCarrito = isset($carrito[$producto->id]); 
                            $cantidad = $enCarrito ? $carrito[$producto->id]['cantidad'] : 0;
                        @endphp

                        <button wire:click="addToCart({{ $producto->id }})" 
                            class="relative bg-white rounded border flex flex-col overflow-hidden group transition-all duration-75 active:scale-95 text-left h-32
                            {{ $enCarrito ? 'border-blue-500 ring-1 ring-blue-500 shadow-md' : 'border-gray-300 hover:border-gray-400' }}">
                            
                            @if($enCarrito)
                                <div class="absolute top-1 right-1 bg-blue-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow-sm z-10">
                                    {{ $cantidad }}
                                </div>
                            @endif

                            <div class="h-16 bg-gray-100 w-full flex items-center justify-center text-2xl overflow-hidden shrink-0">
                                @if($producto->imagen)
                                    <img src="{{ asset('storage/' . $producto->imagen) }}" class="h-full w-full object-cover">
                                @else
                                    <span class="opacity-40 select-none">🍽️</span>
                                @endif
                            </div>

                            <div class="p-1.5 flex flex-col justify-between flex-1 bg-white">
                                <div class="font-bold text-gray-700 text-[11px] leading-tight line-clamp-2">
                                    {{ $producto->nombre }}
                                </div>
                                <div class="font-black text-gray-900 text-sm">
                                    ${{ number_format($producto->precio, 0) }}
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div :class="mobileTab === 'cart' ? 'flex' : 'hidden lg:flex'" 
             class="w-full lg:w-[30%] flex-col h-full bg-white relative z-20 shadow-xl lg:shadow-none">
            
            <div class="lg:hidden p-2 bg-gray-800 text-white flex justify-between items-center">
                <span class="font-bold">🛒 Carrito</span>
                <button @click="mobileTab = 'products'" class="text-xs bg-gray-700 px-2 py-1 rounded">Cerrar</button>
            </div>

            <div class="flex text-[11px] font-bold border-b border-gray-200 shrink-0">
                @foreach(['Mostrador' => '🛍️ Llevar', 'Mesa' => '🍽️ Mesa', 'Domicilio' => '🛵 Domicilio'] as $tipo => $label)
                    <button wire:click="$set('tipo_servicio', '{{ $tipo }}')" 
                        class="flex-1 py-2 text-center transition border-b-2 hover:bg-gray-50
                        {{ $tipo_servicio == $tipo ? 'border-blue-600 text-blue-700 bg-blue-50/50' : 'border-transparent text-gray-400' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div class="bg-gray-50 p-2 border-b border-gray-200 shrink-0 text-xs">
                @if($tipo_servicio == 'Mesa')
                    <div class="flex items-center gap-2">
                        <label class="font-bold text-gray-500 w-12 text-right">MESA:</label>
                        <select wire:model="numero_mesa" class="flex-1 border border-gray-300 rounded p-1 font-bold text-gray-800 focus:border-blue-500 outline-none">
                            <option value="">--</option>
                            @for($i=1; $i<=15; $i++) <option value="{{ $i }}">{{ $i }}</option> @endfor
                        </select>
                    </div>
                @elseif($tipo_servicio == 'Mostrador')
                     <input wire:model="cliente_nombre" type="text" placeholder="Nombre Cliente (Opcional)" 
                        class="w-full border border-gray-300 rounded p-1 text-xs focus:border-blue-500 outline-none font-semibold">
                @elseif($tipo_servicio == 'Domicilio')
                    <div class="space-y-1">
                        <div class="flex gap-1">
                            <input wire:model="cliente_telefono" type="tel" placeholder="Teléfono" class="w-1/3 border border-gray-300 rounded p-1 focus:border-blue-500 outline-none">
                            <input wire:model="cliente_nombre" type="text" placeholder="Nombre" class="w-2/3 border border-gray-300 rounded p-1 focus:border-blue-500 outline-none">
                        </div>
                        <input wire:model="cliente_direccion" type="text" placeholder="Dirección" class="w-full border border-gray-300 rounded p-1 focus:border-blue-500 outline-none">
                        <input wire:model.live="costo_envio" type="number" placeholder="Costo Envío" class="w-full border border-gray-300 rounded p-1 text-right focus:border-blue-500 outline-none">
                    </div>
                @endif
            </div>

            <div class="flex-1 overflow-y-auto p-0 bg-white custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-100 text-[10px] uppercase text-gray-500 sticky top-0 z-10 shadow-sm">
                        <tr>
                            <th class="py-1 px-2">Prod</th>
                            <th class="py-1 px-1 text-center">Cant</th>
                            <th class="py-1 px-2 text-right">Total</th>
                            <th class="w-6"></th>
                        </tr>
                    </thead>
                    <tbody class="text-xs">
                        @forelse($carrito as $id => $item)
                        <tr class="border-b border-gray-100 hover:bg-blue-50 group transition-colors">
                            <td class="py-2 px-2 align-middle">
                                <div class="font-bold text-gray-700 truncate max-w-[140px]">{{ $item['nombre'] }}</div>
                                <input type="text" wire:model.blur="carrito.{{ $id }}.observacion" placeholder="+Nota" 
                                    class="w-full bg-transparent text-[10px] text-gray-500 placeholder-gray-300 outline-none border-none p-0 h-4 focus:ring-0">
                            </td>
                            <td class="py-2 px-1 align-middle text-center">
                                <div class="flex items-center justify-center bg-gray-200 rounded overflow-hidden">
                                    <button wire:click="decrement({{ $id }})" class="w-5 h-5 flex items-center justify-center hover:bg-gray-300 font-bold text-gray-600 active:bg-gray-400">-</button>
                                    <span class="w-6 text-center font-bold text-gray-800 text-[11px]">{{ $item['cantidad'] }}</span>
                                    <button wire:click="increment({{ $id }})" class="w-5 h-5 flex items-center justify-center hover:bg-gray-300 font-bold text-blue-600 active:bg-gray-400">+</button>
                                </div>
                            </td>
                            <td class="py-2 px-2 align-middle text-right font-bold text-gray-800">
                                ${{ number_format($item['precio'] * $item['cantidad'], 0) }}
                            </td>
                            <td class="py-2 px-1 align-middle text-center">
                                <button wire:click="removeItem({{ $id }})" class="text-gray-300 hover:text-red-500 transition-colors">×</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-gray-400 text-xs italic">
                                Carrito vacío
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-gray-50 border-t border-gray-300 p-3 shrink-0 z-20 shadow-[0_-4px_10px_rgba(0,0,0,0.05)]">
                
                <div class="flex justify-between items-end mb-2">
                    <div class="text-[10px] text-gray-500 font-bold uppercase">
                        {{ $articulosCount }} Items
                        @if($costo_envio > 0) <span class="text-blue-600">+Env ${{ number_format($costo_envio) }}</span> @endif
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] text-gray-400 font-bold">TOTAL A PAGAR</div>
                        <div class="text-2xl font-black text-gray-900 leading-none">${{ number_format($total, 0) }}</div>
                    </div>
                </div>

                <div class="flex gap-1 mb-2">
                    <select wire:model.live="metodo_pago" class="flex-1 text-xs border border-gray-300 rounded h-8 bg-white font-bold focus:border-gray-500 outline-none">
                        <option value="Efectivo">💵 Efectivo</option>
                        <option value="Transferencia">🏦 Transf.</option>
                        <option value="Mixto">🔀 Mixto</option>
                    </select>
                    <select wire:model="estado_pago" class="w-24 text-xs border border-gray-300 rounded h-8 font-bold outline-none {{ $estado_pago == 'Pagado' ? 'bg-green-100 text-green-800 border-green-300' : 'bg-yellow-50 text-yellow-800 border-yellow-300' }}">
                        <option value="Pagado">Pagado</option>
                        <option value="Pendiente">Pendiente</option>
                    </select>
                </div>

                @if($metodo_pago == 'Mixto')
                <div class="flex gap-2 mb-2">
                    <div class="relative w-1/2 group">
                        <span class="absolute top-0.5 left-2 text-[8px] font-black text-green-600/60 uppercase pointer-events-none tracking-wider">EFECTIVO</span>
                        <input wire:model.live="pago_efectivo_input" type="number" 
                            class="w-full text-sm border border-gray-300 rounded h-9 pl-2 pt-2 font-bold focus:ring-1 focus:ring-green-500 outline-none text-gray-800 bg-white"
                            placeholder="$0">
                    </div>

                    <div class="relative w-1/2 group">
                        <span class="absolute top-0.5 left-2 text-[8px] font-black text-blue-600/60 uppercase pointer-events-none tracking-wider">TRANSFERENCIA</span>
                        <input wire:model.live="pago_transferencia_input" type="number" 
                            class="w-full text-sm border border-gray-300 rounded h-9 pl-2 pt-2 font-bold focus:ring-1 focus:ring-blue-500 outline-none text-gray-800 bg-white"
                            placeholder="$0">
                    </div>
                </div>
                @endif

                <button wire:click="cobrar" 
                    @if(!$cajaAbierta) disabled @endif
                    class="w-full py-3 rounded bg-gray-900 hover:bg-black text-white font-bold text-sm shadow transition-transform active:scale-95 flex justify-center items-center gap-2 disabled:bg-gray-400 disabled:cursor-not-allowed">
                    <span wire:loading.remove>{{ $cajaAbierta ? 'COBRAR' : 'CAJA CERRADA' }}</span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        ...
                    </span>
                </button>

                @if (session()->has('mensaje'))
    <div class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-red-600 text-white px-8 py-6 rounded-lg shadow-2xl z-[9999] text-2xl font-black animate-bounce text-center border-4 border-white">
        ⚠️ {{ session('mensaje') }}
    </div>
@endif
            </div>
        </div>
    </div>

    <button @click="mobileTab = 'cart'" class="lg:hidden fixed bottom-4 right-4 bg-blue-600 text-white w-14 h-14 rounded-full shadow-2xl flex items-center justify-center font-bold z-50">
        {{ $articulosCount }}
    </button>

</div>