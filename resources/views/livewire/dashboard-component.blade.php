<div class="space-y-6">
    
    <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow-sm">
        <div class="flex items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Panel de Control</h2>
                <p class="text-sm text-gray-500">Resumen operativo del día</p>
            </div>
            
            <a href="{{ route('reporte.diario') }}" target="_blank" class="flex items-center bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 shadow transition text-sm font-bold">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Reporte PDF
            </a>
        </div>

        <div class="text-right">
            <span class="block text-xl font-bold text-blue-600">{{ now()->format('h:i A') }}</span>
            <span class="text-sm text-gray-500">{{ now()->isoFormat('D MMM, YYYY') }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="bg-white p-4 rounded-xl shadow border-b-4 border-blue-600 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Venta Total</p>
                <div class="p-2 bg-blue-100 rounded-full text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">${{ number_format($ingresosTotal, 0) }}</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow border-b-4 border-green-500 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Efectivo en Caja</p>
                <div class="p-2 bg-green-100 rounded-full text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">${{ number_format($ingresosEfectivo, 0) }}</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow border-b-4 border-purple-500 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Digital / Bancos</p>
                <div class="p-2 bg-purple-100 rounded-full text-purple-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">${{ number_format($ingresosDigital, 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Nequi, Daviplata, Tarjetas</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow border-b-4 border-orange-400 hover:shadow-lg transition">
            <div class="flex justify-between h-full">
                <div class="flex flex-col justify-between">
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Pedidos Hoy</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $ventasHoy }}</p>
                </div>
                
                @if($productosBajosStock > 0)
                <div class="flex flex-col items-end justify-between">
                    <p class="text-xs text-red-500 font-bold uppercase tracking-wider">Stock Bajo</p>
                    <div class="flex items-center text-red-600 animate-pulse">
                        <svg class="w-6 h-6 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span class="text-xl font-bold">{{ $productosBajosStock }}</span>
                    </div>
                </div>
                @else
                <div class="flex items-end">
                     <span class="text-green-500 text-xs font-bold bg-green-100 px-2 py-1 rounded">Stock OK</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        
        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
            <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">Ingresos Últimos 7 Días</h3>
            <div id="chartVentas"></div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
            <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">Top 5 Productos Más Vendidos</h3>
            <div id="chartProductos"></div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            
            // 1. CONFIGURACIÓN GRÁFICA VENTAS (Área)
            var optionsVentas = {
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                series: [{
                    name: 'Ventas ($)',
                    data: @json($montosSemana) // Datos desde PHP
                }],
                xaxis: {
                    categories: @json($diasSemana), // Días desde PHP
                    labels: { style: { colors: '#64748b', fontSize: '12px' } }
                },
                yaxis: {
                    labels: { 
                        formatter: function (value) { return "$" + value.toLocaleString(); },
                        style: { colors: '#64748b' }
                    }
                },
                colors: ['#2563EB'], // Azul Tailwind
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.2,
                        stops: [0, 90, 100]
                    }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                grid: { borderColor: '#f1f5f9' }
            };

            // Renderizar Gráfica 1 si existe el contenedor
            if(document.querySelector("#chartVentas")) {
                var chart1 = new ApexCharts(document.querySelector("#chartVentas"), optionsVentas);
                chart1.render();
            }

            // 2. CONFIGURACIÓN GRÁFICA PRODUCTOS (Barras Horizontales)
            var optionsProductos = {
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                series: [{
                    name: 'Unidades Vendidas',
                    data: @json($topProductosCantidades) // Datos desde PHP
                }],
                xaxis: {
                    categories: @json($topProductosNombres), // Nombres desde PHP
                    labels: { style: { colors: '#64748b', fontSize: '12px' } }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: true,
                        barHeight: '50%',
                        distributed: true // Colores diferentes por barra
                    }
                },
                colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'], // Paleta de colores variada
                dataLabels: { enabled: true },
                legend: { show: false },
                grid: { borderColor: '#f1f5f9', xaxis: { lines: { show: true } } }
            };

            // Renderizar Gráfica 2 si existe el contenedor
            if(document.querySelector("#chartProductos")) {
                var chart2 = new ApexCharts(document.querySelector("#chartProductos"), optionsProductos);
                chart2.render();
            }
        });
    </script>
</div>