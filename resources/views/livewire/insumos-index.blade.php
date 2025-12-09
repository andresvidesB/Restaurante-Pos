<div>
    @if($isOpen)
    @include('livewire.insumos-create')
@endif
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-700">Gestión de Inventario</h2>
        <button wire:click="create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 shadow-md">
    + Nuevo Insumo
</button>
    </div>

    <div class="mb-4">
        <input wire:model.live="search" type="text" 
               class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-2" 
               placeholder="Buscar insumo (ej: Pan, Carne)...">
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                <tr>
                    <th class="py-3 px-6">Nombre</th>
                    <th class="py-3 px-6 text-center">Stock Actual</th>
                    <th class="py-3 px-6 text-center">Unidad</th>
                    <th class="py-3 px-6 text-center">Estado</th>
                    <th class="py-3 px-6 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @foreach($insumos as $insumo)
                <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                    <td class="py-3 px-6 font-medium">{{ $insumo->nombre }}</td>
                    
                    <td class="py-3 px-6 text-center font-bold text-lg">
                        {{ $insumo->stock_actual }}
                    </td>

                    <td class="py-3 px-6 text-center">
                        <span class="bg-gray-200 text-gray-600 py-1 px-3 rounded-full text-xs">
                            {{ $insumo->unidad_medida }}
                        </span>
                    </td>

                    <td class="py-3 px-6 text-center">
                        @if($insumo->stock_actual <= $insumo->stock_minimo)
                            <span class="bg-red-100 text-red-600 py-1 px-3 rounded-full text-xs font-bold animate-pulse">
                                ¡Bajo Stock!
                            </span>
                        @else
                            <span class="bg-green-100 text-green-600 py-1 px-3 rounded-full text-xs">
                                Normal
                            </span>
                        @endif
                    </td>

                    <td class="py-3 px-6 text-center">
                        <div class="flex item-center justify-center space-x-2">
                            <button class="text-blue-500 hover:text-blue-700">
                                Editar
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="p-4">
            {{ $insumos->links() }}
        </div>
    </div>
</div>