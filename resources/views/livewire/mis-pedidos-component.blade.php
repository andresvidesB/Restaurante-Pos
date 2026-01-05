<div class="min-h-screen bg-[#1a1a1a] font-sans text-gray-200" style="background-image: url('https://www.transparenttextures.com/patterns/dark-matter.png');">
    
    {{-- Navbar Simple --}}
    <nav class="bg-[#121212]/90 backdrop-blur-md border-b border-white/10 p-4 sticky top-0 z-40">
        <div class="max-w-4xl mx-auto flex justify-between items-center">
            <a href="/" class="flex items-center gap-2 text-gray-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span class="font-bold text-sm uppercase">Volver al Menú</span>
            </a>
            <span class="text-xl font-black text-white tracking-tighter">Mis <span class="text-orange-500 italic">PEDIDOS</span></span>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-8">

        {{-- Mensajes de Feedback --}}
        @if (session()->has('mensaje'))
            <div class="mb-6 bg-green-500/20 border border-green-500 text-green-400 p-4 rounded-xl text-center font-bold animate-pulse">
                {{ session('mensaje') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-6 bg-red-500/20 border border-red-500 text-red-400 p-4 rounded-xl text-center font-bold">
                {{ session('error') }}
            </div>
        @endif

        <div class="space-y-6">
            @forelse($pedidos as $pedido)
                <div class="bg-[#222] rounded-2xl border border-white/5 overflow-hidden shadow-lg transition hover:border-orange-500/30">
                    
                    {{-- Cabecera del Pedido --}}
                    <div class="p-5 flex flex-wrap justify-between items-center bg-[#1b1b1b] border-b border-white/5 gap-4">
                        <div>
                            <span class="text-[10px] text-gray-500 font-black uppercase tracking-widest">Pedido #{{ $pedido->id }}</span>
                            <p class="text-white font-bold text-sm">{{ $pedido->created_at->format('d M Y - h:i A') }}</p>
                        </div>
                        
                        {{-- Estado con Colores --}}
                        @php
                            $estadoColor = match($pedido->status) {
                                'pendiente' => 'text-yellow-400 bg-yellow-400/10 border-yellow-400/20',
                                'confirmado' => 'text-blue-400 bg-blue-400/10 border-blue-400/20',
                                'entregado' => 'text-green-400 bg-green-400/10 border-green-400/20',
                                'cancelado' => 'text-red-400 bg-red-400/10 border-red-400/20',
                                default => 'text-gray-400 bg-gray-400/10'
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase border {{ $estadoColor }}">
                            {{ $pedido->status }}
                        </span>
                    </div>

                    {{-- Cuerpo del Pedido --}}
                    <div class="p-5">
                        <div class="space-y-2 mb-4">
                            @foreach($pedido->details as $detalle)
                                <div class="flex justify-between text-sm text-gray-300">
                                    <span>{{ $detalle->quantity }}x {{ $detalle->product_name }}</span>
                                    <span class="font-mono">${{ number_format($detalle->price * $detalle->quantity, 0) }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Nota si existe --}}
                        @if($pedido->notes)
                            <div class="bg-black/20 p-3 rounded-lg border border-white/5 mb-4">
                                <p class="text-[10px] text-orange-500 font-bold uppercase mb-1">Nota:</p>
                                <p class="text-xs text-gray-400 italic">"{{ $pedido->notes }}"</p>
                            </div>
                        @endif

                        <div class="flex justify-between items-end border-t border-white/10 pt-4">
                            <div>
                                <p class="text-[10px] text-gray-500 uppercase">Total Pagado</p>
                                <p class="text-xl font-black text-white">${{ number_format($pedido->total_with_delivery, 0) }}</p>
                            </div>

                            {{-- LÓGICA DEL BOTÓN CANCELAR (Solo si < 10 min y pendiente) --}}
                            @if($pedido->status === 'pendiente' && $pedido->created_at->diffInMinutes(now()) <= 10)
                                <button 
                                    wire:click="cancelarPedido({{ $pedido->id }})"
                                    wire:confirm="¿Estás seguro de cancelar este pedido?"
                                    class="bg-red-600/10 text-red-500 border border-red-600/50 px-4 py-2 rounded-lg text-xs font-bold uppercase hover:bg-red-600 hover:text-white transition">
                                    Cancelar Pedido
                                </button>
                            @elseif($pedido->status === 'pendiente')
                                <span class="text-[10px] text-gray-500 italic">Tiempo de cancelación expirado</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-20 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    <p class="uppercase font-bold tracking-widest text-sm">Aún no tienes pedidos</p>
                    <a href="/" class="mt-4 inline-block text-orange-500 border-b border-orange-500 pb-0.5 hover:text-white hover:border-white transition">Ir a pedir algo rico</a>
                </div>
            @endforelse
        </div>
    </div>
</div>