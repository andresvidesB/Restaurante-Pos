<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">📁 Gestión de Categorías</h2>
        <button wire:click="create" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
            + Nueva Categoría
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($categorias as $cat)
                <tr>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $cat->nombre }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $cat->descripcion ?? 'Sin descripción' }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button wire:click="edit({{ $cat->id }})" class="text-blue-600 hover:text-blue-900">Editar</button>
                        <button onclick="confirm('¿Borrar?') || event.stopImmediatePropagation()" wire:click="delete({{ $cat->id }})" class="text-red-600 hover:text-red-900">Eliminar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t">
            {{ $categorias->links() }}
        </div>
    </div>

    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-2xl">
            <h3 class="text-lg font-bold mb-4">{{ $categoria_id ? 'Editar' : 'Nueva' }} Categoría</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" wire:model="nombre" class="w-full border rounded-lg p-2 focus:ring-blue-500">
                    @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Descripción (Opcional)</label>
                    <textarea wire:model="descripcion" class="w-full border rounded-lg p-2"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button wire:click="closeModal" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded">Cancelar</button>
                <button wire:click="store" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Guardar</button>
            </div>
        </div>
    </div>
    @endif
</div>