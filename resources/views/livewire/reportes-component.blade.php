<div class="p-6">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
        
        <div class="bg-slate-800 px-6 py-4 border-b border-slate-700">
            <h2 class="text-xl font-bold text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Generador de Reportes Históricos
            </h2>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Tipo de Reporte</label>
                    <div class="flex gap-4 mb-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" wire:model.live="tipoReporte" value="dia" class="form-radio text-blue-600 h-5 w-5">
                            <span class="ml-2 text-gray-700">Un solo día</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" wire:model.live="tipoReporte" value="rango" class="form-radio text-blue-600 h-5 w-5">
                            <span class="ml-2 text-gray-700">Rango de fechas</span>
                        </label>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                {{ $tipoReporte == 'dia' ? 'Seleccionar Fecha' : 'Fecha Inicio' }}
                            </label>
                            <input type="date" wire:model="fechaInicio" class="w-full border rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                            @error('fechaInicio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        @if($tipoReporte == 'rango')
                        <div x-data x-show="$wire.tipoReporte === 'rango'" x-transition>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Fecha Fin</label>
                            <input type="date" wire:model="fechaFin" class="w-full border rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                            @error('fechaFin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        @endif
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('reporte.generar', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => ($tipoReporte == 'rango' ? $fechaFin : $fechaInicio)]) }}" 
                           target="_blank"
                           class="block w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg text-center shadow-lg transition transform hover:-translate-y-1 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            DESCARGAR PDF
                        </a>
                    </div>
                </div>

                <div class="bg-blue-50 rounded-xl p-6 border border-blue-100 flex flex-col justify-center items-center text-center">
                    <div class="bg-white p-4 rounded-full shadow-md mb-4">
                        <svg class="w-16 h-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-blue-900 mb-2">Historial de Ventas</h3>
                    <p class="text-sm text-blue-700">
                        Selecciona un rango de fechas para auditar el desempeño de tu negocio. El reporte incluirá:
                    </p>
                    <ul class="text-sm text-left text-gray-600 mt-4 space-y-2">
                        <li>✅ Total de ingresos reales (Caja vs Bancos)</li>
                        <li>✅ Productos más vendidos en el periodo</li>
                        <li>✅ Cuentas por cobrar pendientes</li>
                        <li>✅ Ventas anuladas</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>