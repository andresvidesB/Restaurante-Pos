<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Venta;
use App\Models\Producto;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\Caja;

class PedidosComponent extends Component
{
    use WithPagination;

    public $filtroTipo = 'Todos'; // Para filtrar: Todos, Domicilio, Mesa
    public $filtroEstado = 'Todos'; // Pendiente, Pagado

    // --- VARIABLES PARA ANULACIÓN ---
    public $ventaIdAnular = null;
    public $motivoAnulacion = '';
    public $mostrarModalAnular = false;

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
    // 1. VERIFICAR CAJA
    $cajaAbierta = Caja::where('user_id', auth()->id())
                        ->whereNull('fecha_cierre')
                        ->exists();

    if (!$cajaAbierta) {
        session()->flash('mensaje', '⛔ ERROR: No puedes cobrar pedidos sin una caja abierta.');
        return;
    }

    // 2. PROCESO NORMAL
    $venta = Venta::find($ventaId);
    if($venta && $venta->estado == 'Pendiente') {
        $venta->update(['estado' => 'Pagado']);
        session()->flash('mensaje', 'Pedido #' . $venta->codigo_factura . ' marcado como PAGADO.');
    }
}

    // --- ACCIONES DE ANULACIÓN (CON MODAL) ---

    // 1. Abrir el modal y preparar variables
    public function confirmarAnulacion($ventaId)
    {
        $this->ventaIdAnular = $ventaId;
        $this->motivoAnulacion = ''; // Limpiar motivo anterior por seguridad
        $this->mostrarModalAnular = true;
    }

    // 2. Cerrar el modal sin hacer nada
    public function cerrarModal()
    {
        $this->mostrarModalAnular = false;
        $this->ventaIdAnular = null;
        $this->motivoAnulacion = '';
    }

    // 3. Ejecutar la anulación real (cuando dan click a "Confirmar" en el modal)
    public function anularPedido()
    {
        // Validamos que exista un ID seleccionado
        if(!$this->ventaIdAnular) return;

        // Validamos que el motivo sea obligatorio
        $this->validate([
            'motivoAnulacion' => 'required|string|min:5|max:255'
        ], [
            'motivoAnulacion.required' => 'Debes indicar el motivo de la anulación.',
            'motivoAnulacion.min' => 'El motivo es muy corto.'
        ]);

        DB::transaction(function () {
            $venta = Venta::find($this->ventaIdAnular);
            
            // Solo anulamos si existe y no está ya anulada
            if($venta && $venta->estado != 'Anulado') {
                
                // A. Devolver Stock al Inventario
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

                // B. Cambiar estado a Anulado Y guardar motivo
                $venta->update([
                    'estado' => 'Anulado',
                    'motivo_anulacion' => $this->motivoAnulacion
                ]);
            }
        });

        // Cerramos modal y notificamos
        $this->cerrarModal();
        session()->flash('mensaje', 'Pedido Anulado y stock restaurado al inventario.');
    }
}