<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Order;

class PaymentComponent extends Component
{
    use WithFileUploads;

    public $order;
    public $payment_method = 'efectivo'; 
    public $payment_proof; 

    public function mount(Order $order)
    {
        $this->order = $order;
        // Si ya está pagado, podrías redirigir, pero dejémoslo abierto por ahora
    }

    public function completePayment()
    {
        $this->validate([
            'payment_method' => 'required|in:efectivo,transferencia',
        ]);

        if ($this->payment_method === 'transferencia') {
            $this->validate(['payment_proof' => 'required|image|max:5120']);
        }

        $proofPath = $this->order->payment_proof; 
        
        if ($this->payment_proof) {
            $proofPath = $this->payment_proof->store('comprobantes', 'public');
        }

        $this->order->update([
            'payment_method' => $this->payment_method,
            'payment_proof' => $proofPath,
            'status' => 'confirmado' // Opcional: cambiar estado
        ]);

        session()->flash('message', '¡Pago registrado correctamente!');
        
        // Notificar a WhatsApp que YA PAGÓ
        $this->notifyPaymentWhatsapp();
    }

    public function notifyPaymentWhatsapp()
    {
        $phone = "573001234567"; // TU NÚMERO
        $msg = "✅ *PAGO REPORTADO (Pedido #{$this->order->id})*\n\n";
        $msg .= "Método: " . ucfirst($this->payment_method) . "\n";
        
        if ($this->payment_method === 'transferencia') {
            $link = asset('storage/' . $this->order->payment_proof);
            $msg .= "📎 Comprobante: " . $link;
        }

        return redirect()->away("https://wa.me/{$phone}?text=" . urlencode($msg));
    }

    public function render()
    {
        return view('livewire.payment-component')->layout('layouts.guest');
    }
}