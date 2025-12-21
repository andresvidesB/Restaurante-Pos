<div class="p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Control de Mermas y Desperdicios</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-red-500 h-fit">
            <h3 class="font-bold text-lg mb-4 flex items-center text-red-600">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Registrar Pérdida
            </h3>

            @if (session()->has('mensaje'))
                <div class="bg-green-100 text-green-700 p-2 rounded mb-3 text-sm font-bold">{{ session('mensaje') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="bg-red-100 text-red-700 p-2 rounded mb-3 text-sm font-bold">{{ session('error') }}</div>
            @endif

            <form wire:submit.prevent="guardarMerma">
                <div class="mb-3">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Producto / Insumo</label>
                    <select wire:model="insumo_id" class="w-full border rounded p-2 focus:ring-2 focus:ring-red-500 outline-none">
                        <option value="">Seleccione...</option>
                        @foreach($insumos as $ins)
                            <option value="{{ $ins->id }}">
                                {{ $ins->nombre }} (Stock: {{ number_format($ins->stock_actual, 0) }} {{ $ins->unidad_medida }})
                            </option>
                        @endforeach
                    </select>
                    @error('insumo_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Cantidad a descontar</label>
                    <input type="number" wire:model="cantidad" step="1" class="w-full border rounded p-2 focus:ring-2 focus:ring-red-500 outline-none" placeholder="Ej: 2">
                    @error('cantidad') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Motivo / Causa</label>
                    <textarea wire:model="motivo" class="w-full border rounded p-2 focus:ring-2 focus:ring-red-500 outline-none" rows="2" placeholder="Ej: Se venció, Se rompió botella..."></textarea>
                    @error('motivo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="w-full bg-red-600 text-white font-bold py-2 rounded hover:bg-red-700 transition">
                    CONFIRMAR BAJA
                </button>
            </form>
        </div>

        <div class="col-span-2">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                    <h3 class="font-bold text-gray-700">Historial de Bajas</h3>
                    
                    <a href="{{ route('reporte.inventario') }}" target="_blank" class="bg-blue-600 text-white px-3 py-1 rounded text-sm font-bold hover:bg-blue-700 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Descargar Reporte Stock PDF
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-100 text-gray-600 uppercase font-bold">
                            <tr>
                                <th class="px-4 py-3">Fecha</th>
                                <th class="px-4 py-3">Insumo</th>
                                <th class="px-4 py-3 text-center">Cantidad</th>
                                <th class="px-4 py-3">Motivo</th>
                                <th class="px-4 py-3">Usuario</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($mermas as $merma)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $merma->created_at->format('d/m/Y h:i A') }}</td>
                                <td class="px-4 py-3 font-bold">{{ $merma->insumo->nombre }}</td>
                                <td class="px-4 py-3 text-center text-red-600 font-bold">
                                    -{{ number_format($merma->cantidad, 0) }} {{ $merma->insumo->unidad_medida }}
                                </td>
                                <td class="px-4 py-3 italic text-gray-500">{{ $merma->motivo }}</td>
                                <td class="px-4 py-3">{{ $merma->usuario->name ?? 'Sistema' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $mermas->links() }}
                </div>
            </div>
        </div>
    </div>
</div>