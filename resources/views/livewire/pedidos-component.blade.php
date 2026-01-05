<div class="p-4 bg-gray-100 min-h-screen">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4 bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <h2 class="text-2xl font-black text-gray-800 flex items-center gap-2">
            📦 Gestión de Pedidos 
            <span class="text-sm font-normal text-gray-500 bg-gray-100 px-2 py-1 rounded-full">{{ $ventas->total() }} total</span>
        </h2>
        
        <div class="flex gap-2 w-full md:w-auto">
            <select wire:model.live="filtroTipo" class="flex-1 border-gray-200 bg-gray-50 rounded-lg text-sm font-bold text-gray-700 h-10">
                <option value="Todos">📦 Todos</option>
                <option value="Domicilio">🛵 Domicilios</option>
                <option value="Mesa">🍽️ Mesas</option>
                <option value="Mostrador">🛍️ Llevar</option>
            </select>
            <select wire:model.live="filtroEstado" class="flex-1 border-gray-200 bg-gray-50 rounded-lg text-sm font-bold text-gray-700 h-10">
                <option value="Todos">📊 Estado</option>
                <option value="Pendiente">⏳ Pendientes</option>
                <option value="Pagado">✅ Pagados</option>
                <option value="Anulado">🚫 Anulados</option>
            </select>
        </div>
    </div>

    @if (session()->has('mensaje'))
        <div class="bg-green-500 text-white px-4 py-3 rounded-lg shadow mb-6 font-bold text-center animate-pulse">
            {{ session('mensaje') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($ventas as $venta)
            <div class="bg-white rounded-xl shadow hover:shadow-lg transition-all border border-gray-200 flex flex-col h-[380px] relative overflow-hidden group">
                
                <div class="absolute left-0 top-0 bottom-0 w-1.5 
                    {{ $venta->estado == 'Pagado' ? 'bg-green-500' : ($venta->estado == 'Anulado' ? 'bg-gray-300' : 'bg-orange-500 animate-pulse') }}">
                </div>

                <div class="p-3 border-b bg-gray-50 pl-4 flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono text-xs text-gray-500 font-bold">#{{ substr($venta->codigo_factura, -4) }}</span>
                            <span class="text-[10px] text-gray-400 uppercase font-bold">{{ $venta->created_at->format('h:i A') }}</span>
                        </div>
                        
                        @if($venta->tipo_servicio == 'Mesa')
                            <h3 class="font-black text-gray-800 text-lg leading-none">🍽️ Mesa {{ $venta->numero_mesa }}</h3>
                        @elseif($venta->tipo_servicio == 'Domicilio')
                            <h3 class="font-black text-blue-600 text-lg leading-none">🛵 Domicilio</h3>
                        @else
                            <h3 class="font-black text-purple-600 text-lg leading-none">🛍️ Para Llevar</h3>
                        @endif

                        @if($venta->cliente_nombre)
                            <p class="text-xs font-bold text-gray-600 mt-1 truncate max-w-[150px]" title="{{ $venta->cliente_nombre }}">
                                👤 {{ $venta->cliente_nombre }}
                            </p>
                        @endif
                    </div>

                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase border 
                        {{ $venta->estado == 'Pagado' ? 'bg-green-50 text-green-600 border-green-200' : ($venta->estado == 'Anulado' ? 'bg-gray-100 text-gray-400 border-gray-200' : 'bg-orange-50 text-orange-600 border-orange-200') }}">
                        {{ $venta->estado }}
                    </span>
                </div>

                <div class="flex-1 p-3 overflow-y-auto bg-white custom-scrollbar pl-4">
                    @if($venta->tipo_servicio == 'Domicilio')
                        <div class="mb-2 p-2 bg-blue-50 rounded border border-blue-100 text-xs text-blue-800">
                            <p>📞 {{ $venta->cliente_telefono }}</p>
                            <p class="truncate">📍 {{ $venta->cliente_direccion }}</p>
                        </div>
                    @endif

                    <div class="space-y-2">
                        @foreach($venta->detalles as $detalle)
                            <div class="flex justify-between items-start text-sm border-b border-gray-100 pb-1 last:border-0">
                                <div class="leading-tight">
                                    <span class="font-bold text-gray-800">{{ $detalle->cantidad }}x</span> 
                                    <span class="text-gray-600 text-xs">{{ $detalle->producto->nombre }}</span>
                                    @if($detalle->observacion)
                                        <div class="text-[10px] text-red-500 italic bg-red-50 px-1 rounded inline-block">📝 {{ $detalle->observacion }}</div>
                                    @endif
                                </div>
                                <span class="font-mono text-xs text-gray-400">${{ number_format($detalle->subtotal, 0) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="p-3 bg-gray-50 border-t pl-4">
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-[10px] uppercase font-bold text-gray-400">Total</span>
                        <span class="text-xl font-black text-gray-800 leading-none">${{ number_format($venta->total, 0) }}</span>
                    </div>
                    
                    @if($venta->estado != 'Anulado')
                        <div class="grid grid-cols-4 gap-1">
                            <a href="{{ route('imprimir.factura', $venta->id) }}" target="_blank" class="col-span-1 flex items-center justify-center bg-white border border-gray-300 rounded hover:bg-gray-100 text-gray-600 py-1" title="Factura">🧾</a>
                            <a href="{{ route('imprimir.comanda', $venta->id) }}" target="_blank" class="col-span-1 flex items-center justify-center bg-white border border-gray-300 rounded hover:bg-yellow-50 text-yellow-600 py-1" title="Comanda">👨‍🍳</a>
                            
                            @if($venta->estado == 'Pendiente')
                                <button wire:click="marcarPagado({{ $venta->id }})" class="col-span-2 bg-green-600 hover:bg-green-700 text-white font-bold text-xs rounded py-1 shadow">
                                    PAGAR
                                </button>
                            @else
                                <button class="col-span-2 bg-gray-200 text-gray-400 font-bold text-xs rounded py-1 cursor-default">
                                    PAGADO
                                </button>
                            @endif
                        </div>
                        <div class="mt-1 text-center">
                             <button wire:click="confirmarAnulacion({{ $venta->id }})" class="text-[10px] text-red-400 hover:text-red-600 font-bold hover:underline">Anular Pedido</button>
                        </div>
                    @else
                        <div class="bg-red-50 text-red-400 text-xs text-center py-1 rounded border border-red-100 italic">
                            Anulado: {{ Str::limit($venta->motivo_anulacion, 20) }}
                        </div>
                    @endif
                </div>

            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $ventas->links() }}
    </div>

    @if($mostrarModalAnular)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-5">
            <h3 class="font-bold text-lg text-gray-800 mb-2">Anular Pedido</h3>
            <textarea wire:model="motivoAnulacion" class="w-full border bg-gray-50 rounded p-2 text-sm focus:ring-2 focus:ring-red-500 outline-none" rows="3" placeholder="Motivo..."></textarea>
            <div class="flex justify-end gap-2 mt-3">
                <button wire:click="cerrarModal" class="px-3 py-1.5 text-gray-500 font-bold text-sm">Cancelar</button>
                <button wire:click="anularPedido" class="px-3 py-1.5 bg-red-500 text-white rounded font-bold text-sm shadow">Confirmar</button>
            </div>
        </div>
    </div>
    @endif
</div>