<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Venta;
use App\Models\Producto;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class PedidosComponent extends Component
{
    use WithPagination;

    public $filtroTipo = 'Todos'; // Para filtrar: Todos, Domicilio, Mesa
    public $filtroEstado = 'Todos'; // Pendiente, Pagado

    public function render()
    {
        $ventas = Venta::query()
            ->when($this->filtroTipo != 'Todos', function($q) {
                return $q->where('tipo_servicio', $this->filtroTipo);
            })
            ->when($this->filtroEstado != 'Todos', function($q) {
                return $q->where('estado', $this->filtroEstado);
            })
            ->orderBy('id', 'desc') // Los más recientes primero
            ->with(['detalles.producto', 'cajero'])
            ->paginate(10);

        return view('livewire.pedidos-component', [
            'ventas' => $ventas
        ])->layout('layouts.app');
    }

    // --- ACCIÓN: MARCAR COMO PAGADO ---
    public function marcarPagado($ventaId)
    {
        $venta = Venta::find($ventaId);
        if($venta && $venta->estado == 'Pendiente') {
            $venta->update(['estado' => 'Pagado']);
            session()->flash('mensaje', 'Pedido #' . $venta->codigo_factura . ' marcado como PAGADO.');
        }
    }

    // --- ACCIÓN: ANULAR PEDIDO (Y DEVOLVER STOCK) ---
    public function anularPedido($ventaId)
    {
        DB::transaction(function () use ($ventaId) {
            $venta = Venta::find($ventaId);
            
            if($venta && $venta->estado != 'Anulado') {
                
                // 1. Devolver Stock al Inventario
                foreach($venta->detalles as $detalle) {
                    // Buscamos el producto y su receta
                    $producto = Producto::find($detalle->producto_id);
                    
                    if($producto) {
                        foreach($producto->insumos as $insumo) {
                            // Calculamos cuánto se gastó: (cantidad vendida * receta)
                            $cantidadRestaurar = $detalle->cantidad * $insumo->pivot->cantidad_requerida;
                            
                            // Lo devolvemos al inventario (Incrementar)
                            $insumo->increment('stock_actual', $cantidadRestaurar);
                        }
                    }
                }

                // 2. Cambiar estado a Anulado
                $venta->update(['estado' => 'Anulado']);
            }
        });

        session()->flash('mensaje', 'Pedido Anulado y stock restaurado al inventario.');
    }
}