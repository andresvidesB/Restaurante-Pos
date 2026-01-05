<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Session;

class CartComponent extends Component
{
    use WithFileUploads;

    public $cart = [];
    public $total = 0;
    
    // Pasos
    public $step = 1; 
    public $currentOrderId;
    public $whatsappUrl = ''; // <--- Nueva variable para guardar el link

    // Formulario
    public $delivery_cost = 0;
    public $note = '';
    public $payment_method = null; 
    public $payment_proof; 

    public function mount()
    {
        $this->cart = Session::get('cart', []);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total = 0;
        foreach ($this->cart as $item) {
            $this->total += $item['price'] * $item['quantity'];
        }
    }
    
    public function getGrandTotalProperty()
    {
        return $this->total + (float)$this->delivery_cost;
    }

    public function goToDetails()
    {
        if (empty($this->cart)) return;
        $this->step = 2;
    }

    public function createOrder()
{
    // 1. Validaciones
    $this->validate([
        'delivery_cost' => 'required|numeric|min:0',
        'note' => 'nullable|string|max:500',
    ]);

    // 2. Crear Orden
    $order = Order::create([
        'user_id' => auth()->id() ?? null,
        'total' => $this->total,
        'delivery_cost' => $this->delivery_cost,
        'total_with_delivery' => $this->getGrandTotalProperty(),
        'status' => 'pendiente',
        'notes' => $this->note,
        'payment_method' => 'pendiente' // Se define en la otra página
    ]);

    // 3. Detalles
    foreach ($this->cart as $id => $item) {
        OrderDetail::create([
            'order_id' => $order->id,
            'product_id' => $id,
            'product_name' => $item['name'],
            'quantity' => $item['quantity'],
            'price' => $item['price']
        ]);
    }

    // 4. Limpiar Carrito
    Session::forget('cart');
    $this->cart = [];

    // ==========================================
    // MAGIA AQUÍ: Generamos el link de pago
    // ==========================================
    $paymentUrl = route('order.pay', $order->id); 

    // 5. Construir Mensaje de WhatsApp
    $phone = "573137163216"; // TU NÚMERO
    $msg = "Hola 🖐️, aquí está mi nuevo pedido #{$order->id}:\n\n";
    
    // ... (lógica de items igual que antes) ...
    
    $msg .= "💰 Total: $" . number_format($order->total_with_delivery, 0) . "\n\n";
    $msg .= "----------------------------------\n";
    $msg .= "👇 *ENLACE PARA PAGAR Y SUBIR FOTO* 👇\n";
    $msg .= $paymentUrl . "\n";
    $msg .= "----------------------------------";

    // 6. Redirigir a WhatsApp
    $url = "https://wa.me/{$phone}?text=" . urlencode($msg);
    return redirect()->away($url);
}

    public function completePayment()
    {
        $this->validate([
            'payment_method' => 'required|in:efectivo,transferencia',
        ]);

        if ($this->payment_method === 'transferencia') {
            $this->validate(['payment_proof' => 'required|image|max:5120']);
        }

        $order = Order::find($this->currentOrderId);
        
        $proofPath = null;
        if ($this->payment_proof) {
            $proofPath = $this->payment_proof->store('comprobantes', 'public');
        }

        $order->update([
            'payment_method' => $this->payment_method,
            'payment_proof' => $proofPath,
        ]);

        // Generar mensaje de confirmación de pago
        $phone = "573137163216"; // TU NÚMERO
        $msg = "💵 *PAGO REPORTADO (Pedido #{$order->id})*\n\n";
        $msg .= "Método: " . ucfirst($this->payment_method) . "\n";
        
        if ($this->payment_method === 'transferencia' && $order->payment_proof) {
            $link = asset('storage/' . $order->payment_proof);
            $msg .= "📎 Comprobante: " . $link;
        }

        $url = "https://wa.me/{$phone}?text=" . urlencode($msg);
        
        // Redirigir y limpiar
        Session::forget('cart');
        $this->cart = [];
        $this->step = 1;
        $this->currentOrderId = null;
        
        return redirect()->away($url);
    }

    public function generateWhatsappUrl($order)
    {
        $phone = "573137163216"; // TU NÚMERO
        $msg = "📋 *NUEVO PEDIDO #{$order->id}*\n\n";
        foreach ($this->cart as $item) {
            $msg .= "- {$item['quantity']}x {$item['name']} \n";
        }
        $msg .= "\n";
        if($order->notes) {
            $msg .= "📝 *Nota:* {$order->notes}\n"; // Incluimos la nota
        }
        $msg .= "💰 Total: $" . number_format($order->total_with_delivery, 0);

        return "https://wa.me/{$phone}?text=" . urlencode($msg);
    }

    public function removeFromCart($productId)
    {
        if(isset($this->cart[$productId])) {
            unset($this->cart[$productId]);
            Session::put('cart', $this->cart);
            $this->calculateTotal();
        }
    }

    public function render()
    {
        return view('livewire.cart-component')->layout('layouts.guest');
    }
}