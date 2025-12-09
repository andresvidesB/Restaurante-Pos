<div class="fixed inset-0 z-50 overflow-y-auto ease-out duration-400">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-50" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            
            <form>
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 border-b pb-2">Nuevo Insumo</h3>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nombre del Insumo</label>
                        <input type="text" wire:model="nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" placeholder="Ej: Pan, Carne, Tomate...">
                        @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                    </div>

                    <div class="flex gap-4">
                        <div class="mb-4 w-1/2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Stock Inicial</label>
                            <input type="number" step="0.01" wire:model="stock_actual" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500">
                            @error('stock_actual') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-4 w-1/2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Stock Mínimo</label>
                            <input type="number" step="0.01" wire:model="stock_minimo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500">
                            @error('stock_minimo') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Unidad de Medida</label>
                        <select wire:model="unidad_medida" class="shadow border rounded w-full py-2 px-3 text-gray-700 bg-white focus:outline-none focus:border-blue-500">
                            <option value="unidad">Unidad (Piezas)</option>
                            <option value="gramos">Gramos (g)</option>
                            <option value="kilogramos">Kilogramos (kg)</option>
                            <option value="ml">Mililitros (ml)</option>
                            <option value="litros">Litros (L)</option>
                        </select>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click.prevent="store()" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Guardar
                    </button>
                    <button wire:click="closeModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>