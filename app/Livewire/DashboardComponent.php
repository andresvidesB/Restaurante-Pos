<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Caja;
use App\Models\Gasto;
use App\Models\DetalleVenta;
use App\Models\Insumo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardComponent extends Component
{
    // --- VARIABLES DE CAJA Y GASTOS ---
    public $cajaAbierta = null;
    public $monto_inicial = 0;
    
    // Variables del Modal de Gastos
    public $descripcion_gasto, $monto_gasto;
    public $mostrarModalGasto = false;

    public function mount()
{
    $this->cajaAbierta = Caja::where('user_id', auth()->id())
                             ->where('estado', 'Abierta')
                             ->first();
}

    // --- ACCIONES DE CAJA ---
    public function abrirCaja()
    {
        $this->validate(['monto_inicial' => 'required|numeric|min:0']);

        Caja::create([
            'user_id' => auth()->id(),
            'monto_inicial' => $this->monto_inicial,
            'fecha_apertura' => now(),
            'estado' => 'Abierta'
        ]);

        return redirect()->route('dashboard');
    }

    public function cerrarCaja()
    {
        if(!$this->cajaAbierta) return;

        $this->cajaAbierta->update([
            'fecha_cierre' => now(),
            'estado' => 'Cerrada',
            'monto_final' => 0 // Aquí luego podrás guardar el arqueo real
        ]);

        return redirect()->route('dashboard');
    }

    public function registrarGasto()
    {
        $this->validate([
            'descripcion_gasto' => 'required',
            'monto_gasto' => 'required|numeric|min:1'
        ]);

        if($this->cajaAbierta) {
            Gasto::create([
                'caja_id' => $this->cajaAbierta->id,
                'descripcion' => $this->descripcion_gasto,
                'monto' => $this->monto_gasto
            ]);
            
            $this->reset(['descripcion_gasto', 'monto_gasto', 'mostrarModalGasto']);
            session()->flash('mensaje', 'Gasto registrado correctamente.');
        }
    }

    public function render()
    {
        $hoy = Carbon::today();

        // 1. KPIs Principales (Tarjetas)
        $ventasHoyCollection = Venta::whereDate('created_at', $hoy)->where('estado', '!=', 'Anulado')->get();
        
        $ingresosTotal = $ventasHoyCollection->sum('total');
        $ventasHoy = $ventasHoyCollection->count();
        
        // Efectivo vs Digital
        $ingresosEfectivo = $ventasHoyCollection->where('metodo_pago', 'Efectivo')->sum('total');
        $ingresosDigital = $ventasHoyCollection->whereIn('metodo_pago', ['Tarjeta', 'Nequi/Daviplata', 'Transferencia'])->sum('total');

        // Stock Bajo (Alerta)
        $productosBajosStock = Insumo::whereColumn('stock_actual', '<=', 'stock_minimo')->count();

        // Gastos del día
        $gastosHoy = 0;
        if($this->cajaAbierta) {
            $gastosHoy = $this->cajaAbierta->gastos()->sum('monto');
        }

        // 2. DATOS PARA GRÁFICA DE VENTAS (Últimos 7 días)
        $montosSemana = [];
        $diasSemana = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            $sumaDia = Venta::whereDate('created_at', $fecha->format('Y-m-d'))
                            ->where('estado', '!=', 'Anulado')
                            ->sum('total');
            
            $montosSemana[] = $sumaDia;
            $diasSemana[] = $fecha->format('d/m');
        }

        // 3. DATOS PARA TOP PRODUCTOS
        $topProductos = DetalleVenta::select('producto_id', DB::raw('sum(cantidad) as total_qty'))
            ->whereHas('venta', function($q){ $q->where('estado', '!=', 'Anulado'); })
            ->groupBy('producto_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->with('producto')
            ->get();

        $topProductosNombres = $topProductos->map(fn($item) => $item->producto->nombre)->toArray();
        $topProductosCantidades = $topProductos->pluck('total_qty')->toArray();

        return view('livewire.dashboard-component', compact(
            'ingresosTotal', 'ventasHoy', 'ingresosEfectivo', 'ingresosDigital', 
            'productosBajosStock', 'gastosHoy',
            'montosSemana', 'diasSemana',
            'topProductosNombres', 'topProductosCantidades'
        ))->layout('layouts.app');
    }
}