<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function generarReporte(Request $request)
    {
        // 1. Definir fechas (Si no envían nada, usa HOY)
        $fechaInicio = $request->input('fecha_inicio') ? Carbon::parse($request->input('fecha_inicio'))->startOfDay() : Carbon::today()->startOfDay();
        $fechaFin = $request->input('fecha_fin') ? Carbon::parse($request->input('fecha_fin'))->endOfDay() : Carbon::today()->endOfDay();

        $esRango = $fechaInicio->format('Y-m-d') !== $fechaFin->format('Y-m-d');

        // 2. Obtener Ventas
        $ventas = Venta::whereBetween('created_at', [$fechaInicio, $fechaFin])
                       ->with('cajero')
                       ->orderBy('created_at', 'desc')
                       ->get();

        // 3. Clasificar
        $ventasValidas = $ventas->where('estado', '!=', 'Anulado');
        $ventasPagadas = $ventasValidas->where('estado', 'Pagado');
        $ventasPendientes = $ventasValidas->where('estado', 'Pendiente');

        // 4. Calcular Totales
        // CORRECCIÓN AQUÍ: Volví a llamar la variable '$totalDia' para que el PDF no falle
        $totalDia = $ventasValidas->sum('total'); 
        
        $totalEfectivo = $ventasPagadas->where('metodo_pago', 'Efectivo')->sum('total');
        $totalDigital = $ventasPagadas->whereIn('metodo_pago', ['Tarjeta', 'Transferencia', 'Nequi/Daviplata'])->sum('total');
        $totalPendiente = $ventasPendientes->sum('total');
                                      
        $cantidadVentas = $ventasValidas->count();
        $cantidadAnuladas = $ventas->where('estado', 'Anulado')->count();

        // 5. Productos Vendidos
        $productosVendidos = DetalleVenta::whereHas('venta', function($q) use ($fechaInicio, $fechaFin){
            $q->whereBetween('created_at', [$fechaInicio, $fechaFin])
              ->where('estado', '!=', 'Anulado');
        })
        ->selectRaw('producto_id, sum(cantidad) as total_cantidad, sum(subtotal) as total_dinero')
        ->groupBy('producto_id')
        ->with('producto')
        ->get();

        // 6. Generar PDF
        $pdf = Pdf::loadView('pdf.reporte_diario', compact(
            'ventas', 
            'totalDia',      // <--- Ahora sí coincide con tu PDF
            'totalEfectivo', 
            'totalDigital',
            'totalPendiente',
            'cantidadVentas',
            'cantidadAnuladas',
            'productosVendidos',
            'fechaInicio',
            'fechaFin',
            'esRango'
        ));

        return $pdf->stream('reporte_ventas.pdf');
    }

    public function reporteInventario()
    {
        // Traemos todos los insumos ordenados
        $insumos = \App\Models\Insumo::orderBy('nombre')->get();
        $hoy = \Carbon\Carbon::now();

        $pdf = Pdf::loadView('pdf.reporte_inventario', compact('insumos', 'hoy'));
        
        return $pdf->stream('reporte_inventario.pdf');
    }
}