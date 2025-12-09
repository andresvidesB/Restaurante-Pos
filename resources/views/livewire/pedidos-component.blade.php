<div class="p-4">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold text-gray-800">Gestión de Pedidos</h2>
        
        <div class="flex gap-2">
            <select wire:model.live="filtroTipo" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="Todos">📦 Todos los Tipos</option>
                <option value="Domicilio">🛵 Solo Domicilios</option>
                <option value="Mesa">🍽️ Solo Mesas</option>
                <option value="Mostrador">🛍️ Mostrador</option>
            </select>

            <select wire:model.live="filtroEstado" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="Todos">Estado: Todos</option>
                <option value="Pendiente">⏳ Pendientes</option>
                <option value="Pagado">✅ Pagados</option>
                <option value="Anulado">🚫 Anulados</option>
            </select>
        </div>
    </div>

    @if (session()->has('mensaje'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 flex items-center animate-bounce">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('mensaje') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($ventas as $venta)
            <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 {{ $venta->estado == 'Pagado' ? 'border-green-500' : ($venta->estado == 'Anulado' ? 'border-gray-400 opacity-75' : 'border-orange-500') }} hover:shadow-lg transition">
                
                <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-start">
                    <div>
                        <span class="text-xs font-bold text-gray-500">#{{ $venta->codigo_factura }}</span>
                        <p class="font-bold text-gray-800 text-sm">
                            {{ $venta->created_at->format('d M, h:i A') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="block text-xs font-bold px-2 py-1 rounded mb-1 {{ $venta->tipo_servicio == 'Domicilio' ? 'bg-blue-100 text-blue-700' : 'bg-gray-200 text-gray-700' }}">
                            {{ $venta->tipo_servicio == 'Mesa' ? 'Mesa ' . $venta->numero_mesa : $venta->tipo_servicio }}
                        </span>
                        <span class="text-xs font-bold px-2 py-1 rounded {{ $venta->estado == 'Pagado' ? 'bg-green-100 text-green-700' : ($venta->estado == 'Anulado' ? 'bg-gray-200 text-gray-600' : 'bg-orange-100 text-orange-700') }}">
                            {{ $venta->estado }}
                        </span>
                    </div>
                </div>

                <div class="p-4">
                    @if($venta->tipo_servicio == 'Domicilio')
                        <div class="mb-3 p-2 bg-blue-50 rounded border border-blue-100 text-sm">
                            <p class="font-bold text-blue-900 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                {{ $venta->cliente_nombre }}
                            </p>
                            <p class="text-gray-600 flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ $venta->cliente_telefono }}
                            </p>
                            <p class="text-gray-600 flex items-center mt-1 truncate" title="{{ $venta->cliente_direccion }}">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $venta->cliente_direccion }}
                            </p>
                        </div>
                    @endif

                    <div class="text-sm text-gray-700 space-y-1 mb-3">
                        @foreach($venta->detalles as $detalle)
                            <div class="flex justify-between border-b border-gray-100 pb-1">
                                <span>{{ $detalle->cantidad }}x {{ $detalle->producto->nombre }}</span>
                                <span class="font-bold text-gray-500">${{ number_format($detalle->subtotal, 0) }}</span>
                            </div>
                        @endforeach
                        
                        @if($venta->costo_envio > 0)
                            <div class="flex justify-between text-blue-600 font-bold pt-1">
                                <span>Domicilio:</span>
                                <span>${{ number_format($venta->costo_envio, 0) }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-between items-center text-lg font-extrabold text-gray-800 border-t pt-2">
                        <span>TOTAL:</span>
                        <span>${{ number_format($venta->total, 0) }}</span>
                    </div>
                    <div class="text-xs text-gray-400 text-right mt-1">Pago: {{ $venta->metodo_pago }}</div>
                </div>

                @if($venta->estado != 'Anulado')
                <div class="bg-gray-50 px-4 py-3 border-t flex justify-end gap-2">
                    
                    <button wire:click="anularPedido({{ $venta->id }})" 
                            wire:confirm="¿Seguro deseas anular este pedido? El stock se devolverá al inventario."
                            class="text-red-500 hover:text-red-700 font-bold text-xs border border-red-200 hover:bg-red-50 px-3 py-2 rounded transition">
                        ANULAR
                    </button>

                    @if($venta->estado == 'Pendiente')
                        <button wire:click="marcarPagado({{ $venta->id }})" 
                                class="bg-green-600 text-white hover:bg-green-700 font-bold text-xs px-4 py-2 rounded shadow transition flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            CONFIRMAR PAGO
                        </button>
                    @endif
                </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $ventas->links() }}
    </div>
</div>