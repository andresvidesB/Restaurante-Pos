<div class="min-h-screen flex items-center justify-center px-4 relative">
    <div class="max-w-md w-full space-y-6 p-10 bg-[#161616]/80 backdrop-blur-xl rounded-3xl border border-white/10 shadow-2xl relative z-10">
        <div class="text-center">
            <h2 class="text-3xl font-black text-white italic tracking-tighter uppercase">
                Únete al <span class="text-orange-600">Fuego</span>
            </h2>
            <p class="mt-2 text-xs text-gray-400 font-medium tracking-widest uppercase">Crea tu cuenta de comensal</p>
        </div>

        <form wire:submit.prevent="register" class="space-y-4">
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1 ml-1">Nombre Completo</label>
                <input wire:model="name" type="text" required 
                    class="w-full px-4 py-3 bg-[#222] border border-white/5 rounded-xl text-white focus:ring-2 focus:ring-orange-600 outline-none transition-all">
                @error('name') <span class="text-red-500 text-[10px] mt-1 ml-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1 ml-1">Correo Electrónico</label>
                <input wire:model="email" type="email" required 
                    class="w-full px-4 py-3 bg-[#222] border border-white/5 rounded-xl text-white focus:ring-2 focus:ring-orange-600 outline-none transition-all">
                @error('email') <span class="text-red-500 text-[10px] mt-1 ml-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1 ml-1">Contraseña</label>
                    <input wire:model="password" type="password" required 
                        class="w-full px-4 py-3 bg-[#222] border border-white/5 rounded-xl text-white focus:ring-2 focus:ring-orange-600 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1 ml-1">Confirmar</label>
                    <input wire:model="password_confirmation" type="password" required 
                        class="w-full px-4 py-3 bg-[#222] border border-white/5 rounded-xl text-white focus:ring-2 focus:ring-orange-600 outline-none transition-all">
                </div>
                @error('password') <div class="col-span-2 text-red-500 text-[10px] mt-1 ml-1">{{ $message }}</div> @enderror
            </div>

            <button type="submit" 
                class="w-full mt-4 py-4 bg-white text-black font-black uppercase tracking-widest rounded-2xl hover:bg-orange-600 hover:text-white transition-all shadow-xl">
                Crear Cuenta
            </button>

            <p class="text-center text-xs text-gray-500 mt-4">
                ¿Ya eres parte de nosotros? 
                <a href="{{ route('login') }}" class="font-bold text-orange-500 hover:underline">Inicia Sesión</a>
            </p>
        </form>
    </div>
</div>