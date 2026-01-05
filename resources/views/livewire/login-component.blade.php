<div class="min-h-screen flex items-center justify-center px-4 relative">
    <div class="max-w-md w-full space-y-8 p-10 bg-[#161616]/80 backdrop-blur-xl rounded-3xl border border-white/10 shadow-2xl relative z-10">
        <div class="text-center">
            <h2 class="text-4xl font-black text-white italic tracking-tighter uppercase">
                Gourmet<span class="text-orange-600">CARBÓN</span>
            </h2>
            <p class="mt-2 text-sm text-gray-400 font-medium tracking-widest uppercase">Bienvenido de nuevo</p>
        </div>

        <form wire:submit.prevent="login" class="mt-8 space-y-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Correo Electrónico</label>
                    <input wire:model="email" type="email" required 
                        class="w-full px-5 py-4 bg-[#222] border border-white/5 rounded-2xl text-white focus:ring-2 focus:ring-orange-600 focus:border-transparent transition-all outline-none"
                        placeholder="admin@correo.com">
                    @error('email') <span class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Contraseña</label>
                    <input wire:model="password" type="password" required 
                        class="w-full px-5 py-4 bg-[#222] border border-white/5 rounded-2xl text-white focus:ring-2 focus:ring-orange-600 focus:border-transparent transition-all outline-none"
                        placeholder="••••••••">
                    @error('password') <span class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center text-gray-400 cursor-pointer">
                    <input type="checkbox" class="rounded border-gray-700 bg-gray-800 text-orange-600 focus:ring-orange-600 mr-2">
                    Recordarme
                </label>
                <a href="{{ route('password.request') }}" class="font-bold text-orange-500 hover:text-orange-400 transition">¿Olvidaste tu clave?</a>
            </div>

            <button type="submit" 
                class="w-full py-4 bg-orange-600 hover:bg-orange-700 text-white font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-orange-900/20 transform active:scale-95 transition-all">
                Encender Sesión
            </button>

            <p class="text-center text-sm text-gray-500">
                ¿No tienes cuenta? 
                <a href="{{ route('register') }}" class="font-bold text-white hover:text-orange-500 transition">Regístrate aquí</a>
            </p>
        </form>
    </div>
</div>