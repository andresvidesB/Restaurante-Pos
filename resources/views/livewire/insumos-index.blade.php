<div class="p-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Inventario de Insumos</h2>
            <p class="text-sm text-gray-500">Gestiona tus ingredientes y existencias</p>
        </div>
        
        <div class="flex gap-2 w-full md:w-auto">
            <div class="relative w-full md:w-64">
                <input wire:model.live="search" type="text" placeholder="Buscar ingrediente..." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition shadow-sm">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            
            <button wire:click="create" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 shadow-md font-semibold whitespace-nowrap flex items-center transition-transform active:scale-95">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Nuevo Insumo
            </button>
        </div>
    </div>

    @if (session()->has('mensaje'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded shadow-sm mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-medium">{{ session('mensaje') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-xs border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 tracking-wider">Nombre</th>
                        <th class="px-6 py-4 text-center tracking-wider">Unidad</th>
                        <th class="px-6 py-4 text-center tracking-wider">Stock Actual</th>
                        <th class="px-6 py-4 text-center tracking-wider">Mínimo</th>
                        <th class="px-6 py-4 text-center tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-right tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($insumos as $insumo)
                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                        <td class="px-6 py-4 font-semibold text-slate-700">
                            {{ $insumo->nombre }}
                        </td>
                        
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                {{ $insumo->unidad_medida }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 text-center">
                            <span class="font-mono text-lg font-bold {{ $insumo->stock_actual <= $insumo->stock_minimo ? 'text-red-600' : 'text-slate-700' }}">
                                {{ number_format($insumo->stock_actual, 0) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <span class="font-mono text-gray-500 font-medium">
                                {{ number_format($insumo->stock_minimo, 0) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center">
                            @if($insumo->stock_actual <= $insumo->stock_minimo)
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200 animate-pulse">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    BAJO
                                </div>
                            @else
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    OK
                                </div>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 text-right space-x-2">
                            <button wire:click="edit({{ $insumo->id }})" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm transition-colors p-1 hover:bg-indigo-50 rounded">
                                Editar
                            </button>
                            <button wire:click="delete({{ $insumo->id }})" 
                                    class="text-red-500 hover:text-red-700 font-medium text-sm transition-colors p-1 hover:bg-red-50 rounded" 
                                    wire:confirm="¿Estás seguro de eliminar este insumo? Si lo usas en recetas, se perderá la referencia.">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($insumos->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $insumos->links() }}
            </div>
        @endif
    </div>

    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm transition-opacity">
        
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all scale-100 overflow-hidden relative">
            
            <div class="bg-slate-900 px-6 py-4 border-b border-slate-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    {{ $insumo_id ? 'Editar Insumo' : 'Crear Nuevo Insumo' }}
                </h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 gap-5">
                    
                    <div>
                        <label class="block text-slate-700 text-sm font-bold mb-1">Nombre del Insumo</label>
                        <input type="text" wire:model="nombre" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-shadow text-gray-900" placeholder="Ej: Pan Hamburguesa">
                        @error('nombre') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-slate-700 text-sm font-bold mb-1">Unidad de Medida</label>
                        <select wire:model="unidad_medida" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                            <option value="unidad">Unidad (ud)</option>
                            <option value="gramos">Gramos (g)</option>
                            <option value="litros">Litros (l)</option>
                            <option value="porción">Porción</option>
                            <option value="paquete">Paquete</option>
                        </select>
                        @error('unidad_medida') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-slate-700 text-sm font-bold mb-1">Stock Inicial</label>
                            <div class="relative">
                                <input type="number" step="1" pattern="\d*" wire:model="stock_actual" class="w-full border border-gray-300 rounded-lg pl-3 pr-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none font-mono font-bold text-slate-800" placeholder="0">
                            </div>
                            @error('stock_actual') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-slate-700 text-sm font-bold mb-1">Alerta Mínimo</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 font-bold text-xs">Min:</div>
                                <input type="number" step="1" pattern="\d*" wire:model="stock_minimo" class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none font-mono text-slate-600" placeholder="10">
                            </div>
                            @error('stock_minimo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                <button wire:click.prevent="store" type="button" class="inline-flex justify-center rounded-lg border border-transparent shadow-md px-5 py-2.5 bg-blue-600 text-base font-bold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all sm:text-sm">
                    {{ $insumo_id ? 'Actualizar' : 'Guardar Insumo' }}
                </button>
                <button wire:click="closeModal" type="button" class="inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all sm:text-sm">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>