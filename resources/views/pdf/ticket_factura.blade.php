<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $venta->codigo_factura }}</title>
    <style>
        /* CSS RESET SIMPLE PARA IMPRESORA TÉRMICA */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Courier New', Courier, monospace;
        }
        
        body {
            font-size: 12px;
            width: 100%;
            max-width: 80mm;
            margin: 0 auto;
            color: #000;
        }

        .ticket { padding: 2mm; }

        .centrado {
            text-align: center;
            align-content: center;
        }

        .titulo {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .subtitulo {
            font-size: 12px;
            margin-bottom: 5px;
        }

        .linea {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .info-pedido {
            margin: 5px 0;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding-bottom: 2px;
        }

        td { padding: 3px 0; }

        .col-cant { width: 15%; text-align: center; }
        .col-desc { width: 55%; }
        .col-precio { width: 30%; text-align: right; }

        .totales {
            margin-top: 5px;
            text-align: right;
            font-size: 12px;
        }

        .total-final {
            font-size: 16px;
            font-weight: bold;
        }

        .pagos {
            margin-top: 5px;
            font-size: 11px;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 10px;
        }
        
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">

    <div class="ticket">
        
        <div class="centrado">
            <h1 class="titulo">RESTAURANTE ÉXITO</h1>
            <p class="subtitulo">Mompós, Bolívar</p>
            <p class="subtitulo">NIT: 900.000.000-1</p>
            <p class="subtitulo">Tel: 300 123 4567</p>
        </div>

        <div class="linea"></div>

        <div class="info-pedido">
            <p><strong>Fecha:</strong> {{ $venta->created_at->format('d/m/Y h:i A') }}</p>
            <p><strong>Factura:</strong> {{ $venta->codigo_factura }}</p>
            <p><strong>Atendió:</strong> {{ $venta->user->name ?? 'Cajero' }}</p>
            
            <div class="linea"></div>

            @if($venta->tipo_servicio == 'Mesa')
                <p style="font-size: 14px"><strong>SERVICIO: MESA #{{ $venta->numero_mesa }}</strong></p>
            
            @elseif($venta->tipo_servicio == 'Domicilio')
                <p><strong>SERVICIO: DOMICILIO</strong></p>
                <p><strong>Cliente:</strong> {{ $venta->cliente_nombre }}</p>
                <p><strong>Tel:</strong> {{ $venta->cliente_telefono }}</p>
                <p><strong>Dir:</strong> {{ $venta->cliente_direccion }}</p>
            
            @else
                <p style="font-size: 14px"><strong>SERVICIO: PARA LLEVAR</strong></p>
                @if($venta->cliente_nombre)
                    <p><strong>Cliente:</strong> {{ strtoupper($venta->cliente_nombre) }}</p>
                @endif
            @endif
        </div>

        <div class="linea"></div>

        <table>
            <thead>
                <tr>
                    <th class="col-cant">CNT</th>
                    <th class="col-desc">DESC</th>
                    <th class="col-precio">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->detalles as $item)
                <tr>
                    <td class="col-cant">{{ $item->cantidad }}</td>
                    <td class="col-desc">
                        {{ $item->producto->nombre }}
                        @if($item->observacion)
                            <br><small style="font-style:italic">({{ $item->observacion }})</small>
                        @endif
                    </td>
                    <td class="col-precio">${{ number_format($item->subtotal, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="linea"></div>

        <div class="totales">
            @if($venta->costo_envio > 0)
                <p>Subtotal: ${{ number_format($venta->total - $venta->costo_envio, 0) }}</p>
                <p>Domicilio: ${{ number_format($venta->costo_envio, 0) }}</p>
            @endif
            <p class="total-final">TOTAL: ${{ number_format($venta->total, 0) }}</p>
        </div>

        <div class="pagos">
            <p><strong>Pago:</strong> {{ $venta->metodo_pago }}</p>
            @if($venta->metodo_pago == 'Mixto')
                <p> - Efectivo: ${{ number_format($venta->pago_efectivo, 0) }}</p>
                <p> - Transf.: ${{ number_format($venta->pago_transferencia, 0) }}</p>
            @endif
        </div>

        <div class="footer">
            <p>¡GRACIAS POR SU COMPRA!</p>
            <p class="subtitulo">Software: Andres Vides POS</p>
        </div>
    </div>
</body>
</html>