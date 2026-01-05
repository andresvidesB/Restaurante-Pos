<div class="container mx-auto p-4 max-w-2xl font-sans" x-data="{ showNote: false }">

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 mb-4 rounded shadow-sm border-l-4 border-red-500">
            <p class="font-bold">⚠️ Revisa los campos pendientes.</p>
        </div>
    @endif

    {{-- PASO 1: CARRITO --}}
    @if($step === 1)
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
            <div class="bg-gray-900 px-6 py-4 flex justify-between items-center">
                <h2 class="text-white text-xl font-bold">🛒 Tu Pedido</h2>
                <span class="bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full">{{ count($cart) }}</span>
            </div>
            <div class="p-6">
                @if(count($cart) > 0)
                    <div class="space-y-4">
                        @foreach($cart as $id => $item)
                            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                                <div>
                                    <h4 class="font-bold text-gray-800">{{ $item['name'] }}</h4>
                                    <p class="text-sm text-gray-500">${{ number_format($item['price']) }} x {{ $item['quantity'] }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="font-bold">${{ number_format($item['price'] * $item['quantity']) }}</span>
                                    <button wire:click="removeFromCart('{{ $id }}')" class="text-red-400 hover:text-red-600">🗑️</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between items-center pt-6 mt-2 border-t">
                        <span class="text-lg font-medium text-gray-600">Total:</span>
                        <span class="text-2xl font-bold text-gray-900">${{ number_format($total) }}</span>
                    </div>
                    <button wire:click="goToDetails" class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg transition transform active:scale-95">
                        Continuar ➡️
                    </button>
                @else
                    <div class="text-center py-10 text-gray-500">Tu carrito está vacío 😢</div>
                    <a href="/" class="block text-center text-blue-600 font-bold mt-2">Ir al Menú</a>
                @endif
            </div>
        </div>

    {{-- PASO 2: DATOS Y NOTAS --}}
    @elseif($step === 2)
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
            <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
                <button wire:click="$set('step', 1)" class="text-gray-500 hover:text-black text-sm font-bold flex items-center gap-1">⬅ Volver</button>
                <h2 class="font-bold text-lg text-gray-800">Detalles</h2>
            </div>
            
            <div class="p-6 space-y-6">
                {{-- Domicilio --}}
                <div>
                    <label class="block font-bold text-gray-800 mb-2">🛵 Costo de Envío</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3 text-gray-400 font-bold">$</span>
                        <input type="number" wire:model.live="delivery_cost" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 text-lg font-bold" placeholder="0">
                    </div>
                </div>

                {{-- AQUÍ ESTÁ EL BOTONCITO PARA LAS NOTAS --}}
                <div>
                    <button 
                        @click="showNote = !showNote" 
                        type="button"
                        class="text-blue-600 font-bold text-sm flex items-center gap-2 hover:underline focus:outline-none">
                        <span x-show="!showNote">➕ Agregar Nota o Instrucción</span>
                        <span x-show="showNote">➖ Ocultar Nota</span>
                    </button>

                    <div x-show="showNote" x-transition class="mt-2">
                        <textarea 
                            wire:model="note" 
                            rows="3" 
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white shadow-inner"
                            placeholder="Ej: Sin cebolla, salsa aparte, timbre dañado..."></textarea>
                    </div>
                </div>

                {{-- Resumen y Botón --}}
                <div class="pt-4 border-t">
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-lg text-gray-600">Total a Pagar:</span>
                        <span class="text-3xl font-extrabold text-gray-900">${{ number_format($this->grandTotal) }}</span>
                    </div>

                    <button 
                        wire:click="createOrder" 
                        wire:loading.attr="disabled"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl shadow-xl transition transform hover:-translate-y-1 flex items-center justify-center gap-2">
                        <span wire:loading.remove>✅ Confirmar Pedido</span>
                        <span wire:loading>Procesando...</span>
                    </button>
                </div>
            </div>
        </div>

    {{-- PASO 3: ENVIAR WHATSAPP Y PAGAR --}}
    @elseif($step === 3)
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border-2 border-green-500 animate-fade-in-up">
            <div class="bg-green-50 px-6 py-6 text-center border-b border-green-100">
                <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3 text-3xl shadow-sm">✓</div>
                <h2 class="text-2xl font-bold text-green-800">¡Pedido Generado!</h2>
                <p class="text-green-700 mt-2 text-sm">Sigue estos 2 pasos para finalizar:</p>
            </div>

            <div class="p-6 space-y-8">
                
                {{-- PASO A: BOTÓN WHATSAPP (LINK REAL) --}}
                <div class="text-center pb-6 border-b border-dashed border-gray-300">
                    <p class="font-bold text-gray-700 mb-3">1. Envía el detalle al restaurante:</p>
                    
                    <a href="{{ $whatsappUrl }}" 
                       target="_blank"
                       class="inline-flex items-center justify-center gap-2 bg-green-500 text-white px-6 py-4 rounded-full font-bold text-lg shadow-lg hover:bg-green-600 hover:shadow-green-500/50 transition transform hover:-translate-y-1 w-full sm:w-auto">
                        <span>📲 Enviar a WhatsApp</span>
                    </a>
                </div>

                {{-- PASO B: PAGO --}}
                <div>
                    <p class="font-bold text-gray-700 mb-4 text-center">2. Selecciona tu forma de pago:</p>

                    <div class="grid grid-cols-2 gap-4">
                        <button 
                            type="button" 
                            wire:click="$set('payment_method', 'efectivo')"
                            class="p-4 border-2 rounded-xl flex flex-col items-center gap-2 transition {{ $payment_method === 'efectivo' ? 'border-blue-500 bg-blue-50 text-blue-700 ring-2 ring-blue-200' : 'border-gray-200 hover:bg-gray-50' }}">
                            <span class="text-3xl">💵</span>
                            <span class="font-bold">Efectivo</span>
                        </button>

                        <button 
                            type="button" 
                            wire:click="$set('payment_method', 'transferencia')"
                            class="p-4 border-2 rounded-xl flex flex-col items-center gap-2 transition {{ $payment_method === 'transferencia' ? 'border-purple-500 bg-purple-50 text-purple-700 ring-2 ring-purple-200' : 'border-gray-200 hover:bg-gray-50' }}">
                            <span class="text-3xl">📱</span>
                            <span class="font-bold">Transferencia</span>
                        </button>
                    </div>

                    {{-- Zona Transferencia --}}
                    @if($payment_method === 'transferencia')
                        <div class="bg-purple-50 p-5 rounded-xl border border-purple-100 mt-4 animate-fade-in">
                            <p class="text-center font-bold text-purple-900 mb-3">Datos Bancarios</p>
                            <div class="bg-white p-3 rounded shadow-sm text-sm mb-4 space-y-1">
                                <p class="flex justify-between"><span>Nequi:</span> <strong>300 123 4567</strong></p>
                                <p class="flex justify-between"><span>Daviplata:</span> <strong>300 987 6543</strong></p>
                            </div>
                            
                            <label class="block font-bold text-sm text-purple-800 mb-2">Sube tu comprobante:</label>
                            <input type="file" wire:model="payment_proof" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-purple-100 file:text-purple-700 hover:file:bg-purple-200">
                            @error('payment_proof') <span class="text-red-500 text-xs block mt-1 font-bold">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    {{-- Botón Final --}}
                    @if($payment_method)
                        <button 
                            wire:click="completePayment"
                            wire:loading.attr="disabled"
                            class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-4 rounded-xl shadow-lg mt-6 transition transform hover:scale-[1.02]">
                            <span wire:loading.remove>Reportar Pago Finalizado</span>
                            <span wire:loading>Enviando...</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>