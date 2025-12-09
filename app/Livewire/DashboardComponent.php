<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Insumo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardComponent extends Component
{
    // 1. Variables para las Tarjetas (KPIs)
    public $ventasHoy;          // Cantidad de pedidos (ej: 15)
    public $ingresosTotal;      // Dinero total (ej: $500.000)
    public $ingresosEfectivo;   // Solo billetes físicos
    public $ingresosDigital;    // Nequi, Tarjeta, Transferencia
    public $productosBajosStock;// Cuántos insumos están en alerta

    // 2. Variables para las Gráficas
    public $diasSemana = [];
    public $montosSemana = [];
    public $topProductosNombres = [];
    public $topProductosCantidades = [];

    public function mount()
    {
        // Fecha de hoy (inicio del día 00:00:00)
        $hoy = Carbon::today();

        // --- A. CÁLCULO DE TARJETAS (KPIs) ---
        
        // Contar pedidos de hoy
        $this->ventasHoy = Venta::whereDate('created_at', $hoy)->count();

        // Sumar TOTAL general vendido hoy
        $this->ingresosTotal = Venta::whereDate('created_at', $hoy)->sum('total');

        // Sumar SOLO Efectivo
        $this->ingresosEfectivo = Venta::whereDate('created_at', $hoy)
                                       ->where('metodo_pago', 'Efectivo')
                                       ->sum('total');

        // Sumar SOLO Digital (Array con los otros métodos)
        $this->ingresosDigital = Venta::whereDate('created_at', $hoy)
                                      ->whereIn('metodo_pago', ['Tarjeta', 'Transferencia', 'Nequi/Daviplata'])
                                      ->sum('total');

        // Contar alertas de stock (Donde stock actual <= stock mínimo)
        $this->productosBajosStock = Insumo::whereColumn('stock_actual', '<=', 'stock_minimo')->count();


        // --- B. DATOS PARA GRÁFICA DE VENTAS (Últimos 7 días) ---
        for ($i = 6; $i >= 0; $i--) {
            // Obtenemos la fecha de hace $i días
            $fecha = Carbon::now()->subDays($i);
            
            // Guardamos el nombre del día (Ej: Lun 12)
            // Nota: Si tu servidor está en inglés saldrá "Mon 12", se puede configurar idioma en config/app.php
            $this->diasSemana[] = $fecha->format('d/m'); 
            
            // Sumamos las ventas de ese día específico
            $monto = Venta::whereDate('created_at', $fecha->format('Y-m-d'))->sum('total');
            $this->montosSemana[] = $monto;
        }


        // --- C. DATOS PARA TOP 5 PRODUCTOS MÁS VENDIDOS ---
        // Hacemos una consulta agrupada para sumar cantidades por producto
        $topProductos = DetalleVenta::select('producto_id', DB::raw('sum(cantidad) as total_vendido'))
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->take(5)
            ->with('producto') // Cargamos la relación para obtener el nombre
            ->get();

        // Separamos los datos para que ApexCharts los entienda
        foreach ($topProductos as $item) {
            // Validamos que el producto exista (por si se borró)
            if($item->producto) {
                $this->topProductosNombres[] = $item->producto->nombre;
                $this->topProductosCantidades[] = $item->total_vendido;
            }
        }
    }

    public function render()
    {
        return view('livewire.dashboard-component')->layout('layouts.app');
    }
}