<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function reporteDiario()
    {
        $hoy = Carbon::today();

        // 1. Obtener TODAS las Ventas de Hoy (incluidas las anuladas para listarlas)
        $ventas = Venta::whereDate('created_at', $hoy)->with('cajero')->get();

        // 2. Separar Ventas Válidas vs Anuladas para los cálculos
        $ventasValidas = $ventas->where('estado', '!=', 'Anulado');
        
        // 3. Calcular Totales (Solo sumamos las válidas)
        $totalDia = $ventasValidas->sum('total');
        
        $totalEfectivo = $ventasValidas->where('metodo_pago', 'Efectivo')
                                       ->sum('total');
                                       
        $totalDigital = $ventasValidas->whereIn('metodo_pago', ['Tarjeta', 'Transferencia', 'Nequi/Daviplata'])
                                      ->sum('total');
                                      
        $cantidadVentas = $ventasValidas->count();
        $cantidadAnuladas = $ventas->where('estado', 'Anulado')->count();

        // 4. Obtener Productos Vendidos (Solo de ventas válidas)
        $productosVendidos = DetalleVenta::whereHas('venta', function($q) use ($hoy){
            $q->whereDate('created_at', $hoy)
              ->where('estado', '!=', 'Anulado'); // <-- Importante: No contar ingredientes de pedidos anulados
        })
        ->selectRaw('producto_id, sum(cantidad) as total_cantidad, sum(subtotal) as total_dinero')
        ->groupBy('producto_id')
        ->with('producto')
        ->get();

        // 5. Generar el PDF
        $pdf = Pdf::loadView('pdf.reporte_diario', compact(
            'ventas', 
            'totalDia', 
            'totalEfectivo', 
            'totalDigital', 
            'cantidadVentas',
            'cantidadAnuladas',
            'productosVendidos',
            'hoy'
        ));

        return $pdf->stream('cierre_caja_' . $hoy->format('d-m-Y') . '.pdf');
    }
}