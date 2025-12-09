<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Diario</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #2c3e50; }
        .header p { margin: 2px 0; color: #777; }
        
        .resumen-box { width: 100%; margin-bottom: 20px; }
        .resumen-table { width: 100%; border-collapse: collapse; }
        .resumen-table td { padding: 8px; border: 1px solid #ddd; }
        .bg-gray { background-color: #f9f9f9; font-weight: bold; }
        
        .section-title { font-size: 16px; font-weight: bold; margin-top: 20px; margin-bottom: 10px; color: #2c3e50; border-bottom: 1px solid #2c3e50; }
        
        .details-table { width: 100%; border-collapse: collapse; font-size: 12px; }
        .details-table th { background-color: #2c3e50; color: white; padding: 6px; text-align: left; }
        .details-table td { border-bottom: 1px solid #ddd; padding: 6px; }
        
        /* Estilos para Anulados */
        .row-anulado { color: #e74c3c; font-style: italic; }
        .text-strike { text-decoration: line-through; opacity: 0.7; }
        .badge-anulado { background-color: #e74c3c; color: white; padding: 2px 4px; border-radius: 3px; font-size: 10px; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>

    <div class="header">
        <h1>RESTAURANTE POS</h1>
        <p>Reporte de Cierre de Caja</p>
        <p>Fecha: {{ $hoy->isoFormat('D [de] MMMM, YYYY') }}</p>
        <p>Generado a las: {{ now()->format('h:i A') }}</p>
    </div>

    <div class="resumen-box">
        <table class="resumen-table">
            <tr>
                <td class="bg-gray">Total Ventas REALES</td>
                <td style="text-align: right; font-size: 16px; font-weight:bold;">${{ number_format($totalDia, 0) }}</td>
            </tr>
            <tr>
                <td class="bg-gray">Efectivo en Caja</td>
                <td style="text-align: right; color: green;">${{ number_format($totalEfectivo, 0) }}</td>
            </tr>
            <tr>
                <td class="bg-gray">Bancos / Digital</td>
                <td style="text-align: right; color: purple;">${{ number_format($totalDigital, 0) }}</td>
            </tr>
            <tr>
                <td class="bg-gray">Pedidos Atendidos</td>
                <td style="text-align: right;">{{ $cantidadVentas }}</td>
            </tr>
            <tr>
                <td class="bg-gray" style="color: #c0392b;">Pedidos Anulados</td>
                <td style="text-align: right; color: #c0392b;">{{ $cantidadAnuladas }}</td>
            </tr>
        </table>
    </div>

    <div class="section-title">Productos Vendidos (Neto)</div>
    <table class="details-table">
        <thead>
            <tr>
                <th>Producto</th>
                <th style="text-align: center">Cant.</th>
                <th style="text-align: right">Total Generado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productosVendidos as $item)
            <tr>
                <td>{{ $item->producto->nombre }}</td>
                <td style="text-align: center">{{ $item->total_cantidad }}</td>
                <td style="text-align: right">${{ number_format($item->total_dinero, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Historial de Transacciones</div>
    <table class="details-table">
        <thead>
            <tr>
                <th>Hora</th>
                <th>Factura</th>
                <th>Estado</th>
                <th>Método Pago</th>
                <th style="text-align: right">Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
            <tr class="{{ $venta->estado == 'Anulado' ? 'row-anulado' : '' }}">
                
                <td>{{ $venta->created_at->format('h:i A') }}</td>
                <td>{{ $venta->codigo_factura }}</td>
                
                <td>
                    @if($venta->estado == 'Anulado')
                        <span class="badge-anulado">ANULADO</span>
                    @else
                        {{ $venta->estado }}
                    @endif
                </td>

                <td>{{ $venta->metodo_pago }}</td>
                
                <td style="text-align: right" class="{{ $venta->estado == 'Anulado' ? 'text-strike' : '' }}">
                    ${{ number_format($venta->total, 0) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Sistema desarrollado por Ing. Samir Vides - Página 1
    </div>

</body>
</html>