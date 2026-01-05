<div class="min-h-screen flex items-center justify-center p-4 bg-[#1a1a1a]" style="background-image: url('https://www.transparenttextures.com/patterns/dark-matter.png');">
    
    <div class="w-full max-w-md bg-[#1a1a1a] border border-white/10 rounded-3xl shadow-2xl overflow-hidden relative">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-orange-600 to-red-600 p-6 text-center relative">
            <h2 class="text-2xl font-black text-white uppercase italic">Confirmar Pago</h2>
            <p class="text-white/80 text-xs font-bold tracking-widest mt-1">PEDIDO #{{ $order->id }}</p>
        </div>

        <div class="p-6 space-y-6">
            
            {{-- Si ya se envió el pago, mostrar éxito --}}
            @if (session()->has('mensaje'))
                <div class="bg-green-600/20 border border-green-500 text-green-400 p-6 rounded-2xl text-center">
                    <div class="text-5xl mb-3">✅</div>
                    <h3 class="text-xl font-bold text-white mb-2">¡Comprobante Enviado!</h3>
                    <p class="text-sm">{{ session('mensaje') }}</p>
                    <a href="/" class="mt-4 inline-block px-6 py-2 bg-green-600 text-white rounded-full font-bold text-sm hover:bg-green-500 transition">Volver al Menú</a>
                </div>
            @else

                {{-- Resumen de Costos --}}
                <div class="bg-[#222] rounded-xl p-5 border border-white/5 space-y-3">
                    <div class="flex justify-between text-gray-400 text-sm">
                        <span>Subtotal Pedido:</span>
                        <span class="text-white font-bold">${{ number_format($order->total, 0) }}</span>
                    </div>
                    
                    {{-- INPUT DOMICILIO (CORREGIDO) --}}
                    <div>
                        <div class="flex justify-between items-center pt-3 border-t border-white/5">
                            <label class="text-orange-500 font-bold text-sm">Costo Domicilio:</label>
                            <div class="relative w-32">
                                <span class="absolute left-3 top-2 text-gray-500 font-bold">$</span>
                                <input 
                                    wire:model.live="input_delivery_cost" 
                                    type="number" 
                                    class="w-full bg-[#161616] border border-orange-500/50 rounded-lg py-1.5 pl-6 pr-2 text-white text-right font-bold focus:ring-1 focus:ring-orange-500 outline-none placeholder-gray-600 transition-all focus:bg-black" 
                                    placeholder="0"
                                >
                            </div>
                        </div>
                        {{-- MENSAJE DE ERROR VISIBLE --}}
                        @error('input_delivery_cost') 
                            <span class="text-red-500 text-[10px] font-bold block text-right mt-1">{{ $message }}</span> 
                        @enderror
                        <p class="text-[10px] text-gray-500 text-right italic mt-1">(Acordado por WhatsApp)</p>
                    </div>

                    {{-- TOTAL FINAL --}}
                    <div class="flex justify-between items-center pt-4 border-t border-white/10 mt-2">
                        <span class="text-lg font-black text-white uppercase tracking-wide">Total a Pagar:</span>
                        <span class="text-3xl font-black text-green-500">${{ number_format($this->granTotal, 0) }}</span>
                    </div>
                </div>

                {{-- Datos Bancarios --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-[#222] p-3 rounded-xl border border-white/5 text-center hover:border-orange-500/30 transition">
                        <p class="text-[10px] uppercase font-bold text-gray-500 mb-1">Nequi</p>
                        <p class="text-white font-mono font-bold text-lg tracking-wider">313 716 3216</p>
                    </div>
                    <div class="bg-[#222] p-3 rounded-xl border border-white/5 text-center hover:border-orange-500/30 transition">
                        <p class="text-[10px] uppercase font-bold text-gray-500 mb-1">Daviplata</p>
                        <p class="text-white font-mono font-bold text-lg tracking-wider">313 716 3216</p>
                    </div>
                </div>

                {{-- Subir Comprobante --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase text-center mb-2">📸 Sube la captura del pago</label>
                    
                    <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-600 rounded-2xl cursor-pointer hover:border-orange-500 hover:bg-orange-500/5 transition group relative overflow-hidden">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 relative z-10">
                            @if($payment_proof)
                                <div class="text-green-500 mb-2">
                                    <svg class="w-10 h-10 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-green-400 font-bold text-sm">¡Imagen Cargada!</p>
                                <p class="text-[10px] text-gray-400 mt-1">Clic para cambiar imagen</p>
                            @else
                                <svg class="w-10 h-10 mb-3 text-gray-500 group-hover:text-orange-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <p class="mb-1 text-sm text-gray-300 font-bold">Toca aquí para subir</p>
                                <p class="text-[10px] text-gray-500">Soporta JPG, PNG (Max 10MB)</p>
                            @endif
                        </div>
                        
                        {{-- Loading Spinner --}}
                        <div wire:loading wire:target="payment_proof" class="absolute inset-0 bg-black/80 flex items-center justify-center z-20">
                            <span class="text-orange-500 font-bold text-sm animate-pulse">Subiendo...</span>
                        </div>

                        <input wire:model="payment_proof" type="file" class="hidden" accept="image/*" />
                    </label>
                    @error('payment_proof') <span class="text-red-500 text-xs font-bold block text-center mt-2">{{ $message }}</span> @enderror
                </div>

                {{-- Botón Final --}}
                <button 
                    wire:click="confirmarPago" 
                    wire:loading.attr="disabled" 
                    class="w-full bg-gradient-to-r from-orange-600 to-red-600 text-white font-black uppercase tracking-widest py-4 rounded-xl shadow-lg hover:from-orange-500 hover:to-red-500 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    
                    <span wire:loading.remove wire:target="confirmarPago">CONFIRMAR PAGO 🚀</span>
                    
                    <span wire:loading wire:target="confirmarPago" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        PROCESANDO...
                    </span>
                </button>

            @endif
        </div>
    </div>
</div>