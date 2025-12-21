<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px; }
        th { background-color: #2c3e50; color: white; padding: 8px; text-align: left; }
        td { border-bottom: 1px solid #ddd; padding: 8px; }
        .low-stock { color: #d35400; font-weight: bold; } /* Alerta stock bajo */
    </style>
</head>
<body>
    <div class="header">
        <h1>RESTAURANTE POS</h1>
        <h3>Reporte General de Existencias</h3>
        <p>Generado: {{ $hoy->format('d/m/Y h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Insumo / Producto</th>
                <th>Categoría</th>
                <th style="text-align: center">Existencia Actual</th>
                <th style="text-align: center">Stock Mínimo</th>
                <th style="text-align: center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($insumos as $insumo)
            <tr>
                <td>#{{ $insumo->id }}</td>
                <td>{{ $insumo->nombre }}</td>
                <td>{{ $insumo->unidad_medida }}</td>
                
                <td style="text-align: center; font-size: 14px; font-weight: bold;">
                    {{ number_format($insumo->stock_actual, 0) }}
                </td>
                
                <td style="text-align: center">
                    {{ number_format($insumo->stock_minimo, 0) }}
                </td>

                <td style="text-align: center">
                    @if($insumo->stock_actual <= $insumo->stock_minimo)
                        <span class="low-stock">⚠️ BAJO</span>
                    @else
                        <span style="color: green;">OK</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>