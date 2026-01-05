<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MisPedidosComponent extends Component
{
    public function render()
    {
        // Obtenemos los pedidos del usuario logueado, del más reciente al más antiguo
        $pedidos = Order::where('user_id', Auth::id())
                        ->with('details') // Traemos los detalles para mostrar qué pidió
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('livewire.mis-pedidos-component', compact('pedidos'))
                ->layout('layouts.guest'); // Usamos tu diseño oscuro
    }

    public function cancelarPedido($orderId)
    {
        $order = Order::find($orderId);

        // 1. Seguridad: Verificar que el pedido sea del usuario actual
        if ($order->user_id !== Auth::id()) {
            return;
        }

        // 2. Verificar Estado: Solo cancelar si está pendiente
        if ($order->status !== 'pendiente') {
            session()->flash('error', 'No se puede cancelar un pedido que ya está en proceso o finalizado.');
            return;
        }

        // 3. REGLA DE ORO: Verificar los 10 minutos
        // diffInMinutes calcula la diferencia entre la creación y "ahora"
        if ($order->created_at->diffInMinutes(now()) > 10) {
            session()->flash('error', 'Han pasado más de 10 minutos. Ya no puedes cancelar el pedido.');
            return;
        }

        // Si pasa todo, cancelamos
        $order->update(['status' => 'cancelado']);
        session()->flash('mensaje', 'Pedido cancelado correctamente.');
    }
}