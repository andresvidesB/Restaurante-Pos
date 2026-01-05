<div class="p-6">
    <div class="flex justify-between mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Gestión de Usuarios</h2>
        <button wire:click="create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Nuevo Usuario
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if($isModalOpen)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg w-1/3 shadow-xl">
                <h3 class="text-xl mb-4 font-bold">{{ $user_id ? 'Editar' : 'Crear' }} Usuario</h3>
                
                <div class="mb-3">
                    <label class="block">Nombre</label>
                    <input type="text" wire:model="name" class="w-full border p-2 rounded">
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="block">Email</label>
                    <input type="email" wire:model="email" class="w-full border p-2 rounded">
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="block">Contraseña {{ $user_id ? '(Dejar vacía para mantener)' : '' }}</label>
                    <input type="password" wire:model="password" class="w-full border p-2 rounded">
                    @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="block">Rol</label>
                    <select wire:model="role" class="w-full border p-2 rounded">
                        <option value="mesero">Mesero</option>
                        <option value="admin">Administrador</option>
                        <option value="cocina">Cocina</option>
                    </select>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button wire:click="closeModal" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
                    <button wire:click="store" class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Nombre</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Rol</th>
                    <th class="p-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ $user->name }}</td>
                        <td class="p-3">{{ $user->email }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded text-xs text-white 
                                {{ $user->role == 'admin' ? 'bg-red-500' : ($user->role == 'mesero' ? 'bg-green-500' : 'bg-gray-500') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            <button wire:click="edit({{ $user->id }})" class="text-blue-600 hover:underline mx-1">Editar</button>
                            @if($user->id !== auth()->id()) <button wire:click="delete({{ $user->id }})" class="text-red-600 hover:underline mx-1" onclick="return confirm('¿Seguro?')">Borrar</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-3">
            {{ $users->links() }}
        </div>
    </div>
</div>