<div class="p-4">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold text-gray-800">Menú y Recetas</h2>
        
        <div class="flex gap-2 w-full md:w-auto">
            <input wire:model.live="search" type="text" placeholder="Buscar producto..." class="border rounded-lg px-4 py-2 w-full md:w-64 focus:ring-2 focus:ring-blue-500 outline-none">
            
            <button wire:click="create" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 shadow-md font-bold whitespace-nowrap flex items-center">
                <span class="mr-2 text-xl">+</span> Nuevo Plato
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($productos as $producto)
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:shadow-lg transition flex flex-col justify-between">
                
                <div class="h-48 w-full bg-gray-200 relative">
                    @if($producto->imagen)
                        <img src="{{ asset('storage/'.$producto->imagen) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 font-bold bg-gray-100">
                            Sin Foto
                        </div>
                    @endif
                    
                    <div class="absolute top-2 right-2">
                        <span class="px-2 py-1 text-xs font-bold rounded-full shadow-sm {{ $producto->activo ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                            {{ $producto->activo ? 'Disponible' : 'Oculto' }}
                        </span>
                    </div>
                </div>

                <div class="p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-xs font-bold text-blue-600 bg-blue-100 px-2 py-1 rounded-full uppercase">
                                {{ $producto->categoria->nombre ?? 'Sin Categoría' }}
                            </span>
                            <h3 class="mt-2 text-xl font-bold text-gray-800 leading-tight">{{ $producto->nombre }}</h3>
                        </div>
                        <div class="text-right ml-2">
                            <span class="block text-xl font-extrabold text-gray-900">${{ number_format($producto->precio, 0) }}</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-dashed">
                        <p class="text-xs text-gray-400 uppercase font-bold mb-2">Ingredientes:</p>
                        <div class="flex flex-wrap gap-2">
                            @forelse($producto->insumos as $insumo)
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded border border-gray-200">
                                    {{ $insumo->nombre }} ({{ floatval($insumo->pivot->cantidad_requerida) }} {{ $insumo->unidad_medida }})
                                </span>
                            @empty
                                <span class="text-xs text-red-400 italic">Sin receta definida</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-5 py-3 border-t flex justify-between items-center">
                    <button wire:click="edit({{ $producto->id }})" class="text-indigo-600 hover:text-indigo-900 font-bold text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Editar
                    </button>

                    <button wire:click="delete({{ $producto->id }})" 
                            wire:confirm="¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer."
                            class="text-red-600 hover:text-red-900 font-bold text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Eliminar
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $productos->links() }}
    </div>

    @if($isOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full relative z-50">
                
                <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-bold text-gray-800">
                        {{ $producto_id ? 'Editar Producto' : 'Crear Nuevo Producto' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 font-bold text-2xl">&times;</button>
                </div>

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del Plato</label>
                            <input wire:model="nombre" type="text" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                            @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Precio</label>
                            <input wire:model="precio" type="number" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                            @error('precio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Categoría</label>
                            <select wire:model="categoria_id" class="w-full border rounded-lg p-2 bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="">Seleccione...</option>
                                @foreach($categorias_list as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                            @error('categoria_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2 md:col-span-1 flex items-center pt-6">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" wire:model="activo" class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                <span class="font-bold text-gray-700 text-sm">Disponible en Menú Web</span>
                            </label>
                        </div>
                        <div class="col-span-2 md:col-span-1 flex items-center pt-2">
    <label class="flex items-center space-x-2 cursor-pointer p-2 border rounded-lg bg-yellow-50 border-yellow-200 hover:bg-yellow-100 transition">
        <input type="checkbox" wire:model="es_oferta" class="w-5 h-5 text-yellow-600 rounded border-gray-300 focus:ring-yellow-500">
        <span class="font-bold text-yellow-800 text-sm">🔥 ¡Marcar como Oferta!</span>
    </label>
</div>

                        <div class="col-span-2 border-t pt-4 mt-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Imagen del Plato (Opcional)</label>
                            
                            <div class="flex items-center gap-4">
                                <div class="flex-1">
                                    <input type="file" wire:model="imagen" class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-blue-50 file:text-blue-700
                                        hover:file:bg-blue-100
                                    "/>
                                    @error('imagen') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden border flex-shrink-0">
                                    @if ($imagen)
                                        <img src="{{ $imagen->temporaryUrl() }}" class="w-full h-full object-cover">
                                    @elseif($imagen_actual)
                                        <img src="{{ asset('storage/'.$imagen_actual) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-xs text-gray-400 text-center p-1">Sin foto</div>
                                    @endif
                                </div>
                            </div>
                            <div wire:loading wire:target="imagen" class="text-blue-600 text-xs mt-1">Cargando imagen...</div>
                        </div>

                    </div>

                    <hr class="border-dashed my-4">

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <h4 class="font-bold text-blue-800 mb-2 flex items-center">
                            Ingredientes de la Receta
                        </h4>
                        
                        <div class="flex gap-2 mb-3">
                            <div class="w-3/5">
                                <select wire:model="insumoSeleccionado" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($insumosDisponibles as $ins)
                                        <option value="{{ $ins->id }}">{{ $ins->nombre }} ({{ $ins->unidad_medida }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-1/5">
                                <input wire:model="cantidadInsumo" type="number" step="0.01" placeholder="Cant." class="w-full border border-gray-300 rounded-lg p-2 text-sm text-center">
                            </div>
                            <button wire:click.prevent="agregarInsumo" class="w-auto bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700 font-bold text-lg">+</button>
                        </div>

                        <div class="bg-white rounded border border-gray-200 overflow-hidden">
                            @if(count($receta) > 0)
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-100 text-gray-600">
                                        <tr>
                                            <th class="py-2 px-3 text-left">Insumo</th>
                                            <th class="py-2 px-3 text-center">Cantidad</th>
                                            <th class="py-2 px-3 text-right"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($receta as $index => $item)
                                            <tr>
                                                <td class="py-2 px-3">{{ $item['nombre'] }}</td>
                                                <td class="py-2 px-3 text-center font-bold">{{ $item['cantidad'] }} {{ $item['unidad'] }}</td>
                                                <td class="py-2 px-3 text-right">
                                                    <button wire:click.prevent="quitarInsumo({{ $index }})" class="text-red-500 hover:text-red-700 font-bold">Eliminar</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="p-4 text-center text-gray-400 text-xs italic">No hay ingredientes agregados.</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end gap-3">
                    <button wire:click="closeModal" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">Cancelar</button>
                    <button wire:click="store" wire:loading.attr="disabled" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold shadow-md disabled:opacity-50">
                        {{ $producto_id ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>