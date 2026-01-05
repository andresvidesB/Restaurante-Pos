<div class="max-w-4xl mx-auto p-6">
    
    <h2 class="text-3xl font-black text-gray-800 mb-6 flex items-center gap-2">
        🏦 Gestión de Caja
    </h2>

    @if(!$cajaAbierta)
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center border border-gray-100">
            <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-4xl">🔓</span>
            </div>
            
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Abrir Nueva Caja</h3>
            <p class="text-gray-500 mb-6">Ingresa el monto de dinero base con el que inicias el turno.</p>

            <div class="max-w-xs mx-auto">
                <label class="block text-left font-bold text-gray-700 mb-1">Monto Inicial ($)</label>
                <input wire:model="monto_inicial" type="number" class="w-full text-center text-2xl font-bold border-2 border-blue-200 rounded-xl p-3 focus:ring-4 focus:ring-blue-100 outline-none transition" placeholder="0">
                @error('monto_inicial') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
                
                <button wire:click="abrirCaja" class="mt-6 w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg transition transform active:scale-95">
                    ABRIR CAJA E IR A VENDER 🚀
                </button>
            </div>
        </div>

    @else
        <div class="grid md:grid-cols-2 gap-6">
            
            <div class="bg-white rounded-2xl shadow-lg p-6 border-t-4 border-green-500">
                <h3 class="font-bold text-gray-500 uppercase text-xs mb-4">Resumen del Turno</h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center border-b border-dashed pb-2">
                        <span class="text-gray-600">📅 Apertura:</span>
                        <span class="font-bold">{{ \Carbon\Carbon::parse($cajaAbierta->fecha_apertura)->format('d M, h:i A') }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">💵 Base Inicial:</span>
                        <span class="font-bold text-gray-800">${{ number_format($cajaAbierta->monto_inicial, 0) }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">📈 Ventas (Pagadas):</span>
                        <span class="font-bold text-green-600">+ ${{ number_format($totalVentas, 0) }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">💸 Gastos/Salidas:</span>
                        <span class="font-bold text-red-500">- ${{ number_format($totalGastos, 0) }}</span>
                    </div>

                    <div class="bg-gray-100 p-3 rounded-lg flex justify-between items-center mt-2">
                        <span class="font-black text-gray-700">TOTAL ESPERADO:</span>
                        <span class="font-black text-2xl text-blue-800">${{ number_format($totalEfectivoEsperado, 0) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 border-t-4 border-red-500 relative">
                @if (session()->has('mensaje'))
                    <div class="absolute inset-0 bg-white/90 flex items-center justify-center z-10 rounded-2xl">
                        <div class="text-center text-green-600 font-bold text-xl animate-bounce">
                            ✅ {{ session('mensaje') }}
                        </div>
                    </div>
                @endif

                <h3 class="font-bold text-gray-500 uppercase text-xs mb-4">Cerrar Caja</h3>
                <p class="text-sm text-gray-400 mb-4">Cuenta el dinero físico y escribe el total real abajo.</p>

                <label class="block font-bold text-gray-700 mb-1">Monto Final Real ($)</label>
                <input wire:model="monto_final" type="number" class="w-full text-2xl font-bold border-2 border-gray-200 rounded-xl p-3 focus:border-red-500 outline-none mb-4" placeholder="0">
                @error('monto_final') <span class="text-red-500 text-sm block -mt-3 mb-3">{{ $message }}</span> @enderror

                <div class="bg-yellow-50 text-yellow-800 p-3 rounded text-xs mb-4">
                    ⚠️ Al cerrar, no podrás realizar más ventas hasta abrir un nuevo turno.
                </div>

                <button wire:click="cerrarCaja" 
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl shadow-lg transition">
                    🔒 CERRAR CAJA
                </button>
            </div>

        </div>
    @endif
</div>