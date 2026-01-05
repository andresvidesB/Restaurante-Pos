<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-xl">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-900">Recuperar Contraseña</h2>
            <p class="mt-2 text-sm text-gray-600">
                Ingresa tu correo y te enviaremos un enlace para restablecerla.
            </p>
        </div>

        @if($status)
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ $status }}
            </div>
        @endif

        <form wire:submit.prevent="sendResetLink" class="mt-8 space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Correo Electrónico</label>
                <input wire:model="email" type="email" class="appearance-none rounded-lg w-full px-3 py-2 border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="correo@ejemplo.com">
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Enviar Enlace
            </button>
            
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">Volver al Login</a>
            </div>
        </form>
    </div>
</div>