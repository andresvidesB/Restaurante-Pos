<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 to-blue-900 py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-2xl relative overflow-hidden border-t-8 border-blue-600">
        
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-50 rounded-full opacity-50 pointer-events-none"></div>

        <div class="text-center relative z-10">
            <div class="mx-auto h-16 w-16 bg-blue-600 text-white rounded-full flex items-center justify-center mb-4 shadow-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
            </div>
            <h2 class="mt-2 text-center text-3xl font-extrabold text-gray-900">
                RESTO-POS
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Bienvenido, ingresa tus credenciales
            </p>
        </div>

        <form class="mt-8 space-y-6 relative z-10" wire:submit.prevent="login">
            <div class="rounded-md shadow-sm -space-y-px">
                
                <div class="mb-4 relative">
                    <label for="email-address" class="sr-only">Correo Electrónico</label>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.064-2.935"></path></svg>
                    </div>
                    <input wire:model="email" id="email-address" name="email" type="email" autocomplete="email" required 
                        class="appearance-none rounded-lg relative block w-full pl-10 pr-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm transition-colors" 
                        placeholder="Correo Electrónico">
                    @error('email') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                </div>
                
                <div class="relative">
                    <label for="password" class="sr-only">Contraseña</label>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input wire:model="password" id="password" name="password" type="password" autocomplete="current-password" required 
                        class="appearance-none rounded-lg relative block w-full pl-10 pr-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm transition-colors" 
                        placeholder="Contraseña">
                    @error('password') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <button type="submit" wire:loading.attr="disabled"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg">
                    
                    <span wire:loading class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    
                    <span wire:loading.remove>
                        INGRESAR AL SISTEMA
                    </span>
                    <span wire:loading>
                        Verificando...
                    </span>
                </button>
            </div>
        </form>
        
        <div class="text-center text-xs text-gray-400 mt-4 relative z-10">
            &copy; {{ date('Y') }} RESTO-POS. By Ing. Samir Vides.
        </div>
    </div>
</div>