<div class="container mx-auto p-4 max-w-lg">
    
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        
        {{-- Encabezado --}}
        <div class="bg-blue-600 px-6 py-4 text-center">
            <h2 class="text-white text-xl font-bold">Gestión de Pago</h2>
            <p class="text-blue-100 text-sm">Pedido #{{ $order->id }}</p>
        </div>

        <div class="p-6">
            
            {{-- Mensaje de Éxito --}}
            @if (session()->has('message'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                    <p class="font-bold">¡Excelente!</p>
                    <p>{{ session('message') }}</p>
                </div>
            @else
                {{-- Resumen Rápido --}}
                <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Total a Pagar:</span>
                        <span class="font-bold text-xl text-gray-900">${{ number_format($order->total_with_delivery, 0) }}</span>
                    </div>
                    @if($order->notes)
                        <div class="text-xs text-gray-500 mt-2 pt-2 border-t">
                            Nota: {{ $order->notes }}
                        </div>
                    @endif
                </div>

                <h3 class="font-bold text-gray-800 mb-4 text-center">Selecciona Método de Pago</h3>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <button wire:click="$set('payment_method', 'efectivo')"
                        class="p-4 border-2 rounded-xl flex flex-col items-center gap-2 transition {{ $payment_method === 'efectivo' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200' }}">
                        <span class="text-2xl">💵</span>
                        <span class="font-bold">Efectivo</span>
                    </button>

                    <button wire:click="$set('payment_method', 'transferencia')"
                        class="p-4 border-2 rounded-xl flex flex-col items-center gap-2 transition {{ $payment_method === 'transferencia' ? 'border-purple-500 bg-purple-50 text-purple-700' : 'border-gray-200' }}">
                        <span class="text-2xl">📱</span>
                        <span class="font-bold">Transferencia</span>
                    </button>
                </div>

                {{-- Zona Transferencia --}}
                @if($payment_method === 'transferencia')
                    <div class="bg-purple-50 p-5 rounded-xl border border-purple-100 mb-6 animate-fade-in">
                        <p class="text-center font-bold text-purple-900 mb-3">Datos Bancarios</p>
                        <div class="bg-white p-3 rounded shadow-sm text-sm mb-4">
                            <p><strong>Nequi:</strong> 300 123 4567</p>
                            <p><strong>Daviplata:</strong> 300 987 6543</p>
                        </div>
                        
                        <label class="block font-bold text-sm text-purple-800 mb-2">Sube tu comprobante:</label>
                        <input type="file" wire:model="payment_proof" accept="image/*" class="w-full text-sm">
                        @error('payment_proof') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>
                @endif

                <button 
                    wire:click="completePayment"
                    wire:loading.attr="disabled"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform hover:scale-[1.02]">
                    <span wire:loading.remove>Confirmar y Reportar Pago</span>
                    <span wire:loading>Enviando...</span>
                </button>
            @endif
        </div>
    </div>
</div>