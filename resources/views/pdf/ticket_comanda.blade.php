<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comanda Cocina #{{ $venta->codigo_factura }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: sans-serif;
        }
        body {
            width: 100%;
            max-width: 80mm;
            margin: 0 auto;
            color: #000;
        }
        .comanda { padding: 5px; }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .tipo-servicio {
            font-size: 22px;
            font-weight: 900;
            display: block;
            text-transform: uppercase;
        }

        /* CAJA NEGRA PARA DATOS */
        .box-datos {
            background: #000;
            color: #fff;
            padding: 8px;
            margin: 5px 0;
            border-radius: 4px;
            text-align: left;
        }
        .box-datos p {
            margin: 2px 0;
            font-size: 16px; /* Letra grande para el nombre */
            font-weight: bold;
        }
        .label-dato {
            font-size: 10px;
            text-transform: uppercase;
            opacity: 0.8;
            font-weight: normal;
            display: block;
        }

        .mesa-grande {
            font-size: 30px;
            font-weight: bold;
            border: 3px solid #000;
            display: inline-block;
            padding: 2px 10px;
            margin-top: 5px;
            border-radius: 5px;
        }

        .item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #444;
        }
        .cant {
            font-size: 24px;
            font-weight: 900;
            min-width: 35px;
            line-height: 1;
        }
        .desc { flex: 1; }
        .prod-name {
            font-size: 16px;
            font-weight: bold;
            line-height: 1.1;
            display: block;
        }
        .obs {
            display: block;
            background: #000;
            color: #fff;
            font-size: 12px;
            padding: 2px 4px;
            margin-top: 3px;
            border-radius: 3px;
            font-weight: bold;
        }

        .meta {
            font-size: 10px;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="comanda">
        
        <div class="header">
            <span class="tipo-servicio">
                @if($venta->tipo_servicio == 'Domicilio') 🛵 DOMICILIO
                @elseif($venta->tipo_servicio == 'Mesa') 🍽️ PARA MESA
                @else 🛍️ PARA LLEVAR
                @endif
            </span>

            @if($venta->tipo_servicio == 'Mesa')
                <div class="mesa-grande">#{{ $venta->numero_mesa }}</div>
            @endif

            @if($venta->tipo_servicio == 'Domicilio')
                <div class="box-datos">
                    <span class="label-dato">Cliente:</span>
                    <p>{{ strtoupper($venta->cliente_nombre) }}</p>
                    
                    <span class="label-dato" style="margin-top: 5px">Teléfono:</span>
                    <p style="font-size: 14px">📞 {{ $venta->cliente_telefono }}</p>
                    
                    <span class="label-dato">Dirección:</span>
                    <p style="font-size: 14px">📍 {{ $venta->cliente_direccion }}</p>
                </div>
            @endif

            @if($venta->tipo_servicio == 'Mostrador' && $venta->cliente_nombre)
                <div class="box-datos">
                    <span class="label-dato">Cliente:</span>
                    <p>👤 {{ strtoupper($venta->cliente_nombre) }}</p>
                </div>
            @endif

            <div style="margin-top: 8px; font-size: 12px; font-weight: bold;">
                Ticket #{{ substr($venta->codigo_factura, -4) }}
            </div>
            <div style="font-size: 10px;">
                {{ $venta->created_at->format('d/m/Y - h:i A') }}
            </div>
        </div>

        @foreach($venta->detalles as $detalle)
            <div class="item">
                <div class="cant">{{ $detalle->cantidad }}</div>
                <div class="desc">
                    <span class="prod-name">{{ $detalle->producto->nombre }}</span>
                    @if($detalle->observacion)
                        <span class="obs">📝 {{ $detalle->observacion }}</span>
                    @endif
                </div>
            </div>
        @endforeach

        <div class="meta">
            Atendió: {{ strtoupper($venta->user->name ?? 'Cajero') }}
            <br>--- FIN DE COMANDA ---
        </div>
    </div>
</body>
</html>