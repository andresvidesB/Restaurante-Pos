<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Order;

class PaymentComponent extends Component
{
    use WithFileUploads;

    public $order;
    public $payment_proof;
    public $input_delivery_cost; // Costo domicilio ingresado por usuario

    public function mount(Order $order)
    {
        $this->order = $order;
        // Si ya hay un costo guardado, lo mostramos. Si no, lo dejamos vacío o en 0.
        $this->input_delivery_cost = $order->delivery_cost > 0 ? $order->delivery_cost : null;
    }

    // Calcula el total en tiempo real para mostrarlo en la vista
    public function getGranTotalProperty()
    {
        $domicilio = is_numeric($this->input_delivery_cost) ? $this->input_delivery_cost : 0;
        return $this->order->total + (float)$domicilio;
    }

    public function confirmarPago()
    {
        // 1. Asegurar que el domicilio sea un número (si está vacío, es 0)
        if($this->input_delivery_cost === '' || $this->input_delivery_cost === null) {
            $this->input_delivery_cost = 0;
        }

        // 2. Validaciones
        $this->validate([
            'payment_proof' => 'required|image|max:10240', // Obligatorio imagen, max 10MB
            'input_delivery_cost' => 'required|numeric|min:0'
        ], [
            'payment_proof.required' => '⚠️ Por favor sube la foto del comprobante.',
            'payment_proof.image' => 'El archivo debe ser una imagen válida.',
            'payment_proof.max' => 'La imagen es muy pesada (máx 10MB).',
            'input_delivery_cost.required' => 'Ingresa el valor del domicilio.',
        ]);

        // 3. Guardar la imagen en el servidor
        // Se guarda en storage/app/public/comprobantes
        $path = $this->payment_proof->store('comprobantes', 'public');

        // 4. Actualizar la Base de Datos
        $this->order->update([
            'delivery_cost' => $this->input_delivery_cost,
            'total_with_delivery' => $this->granTotal,
            'payment_proof' => $path, // Guardamos la ruta de la foto
            'status' => 'confirmado', // Cambiamos estado a confirmado
            'payment_method' => 'transferencia'
        ]);

        // 5. Generar Link Público de la Imagen
        // Esto crea una URL tipo: tusitio.com/storage/comprobantes/foto.jpg
        $urlComprobante = asset('storage/' . $path);

        // 6. Redirigir a WhatsApp con el mensaje y el LINK
        $this->enviarConfirmacionWhatsapp($urlComprobante);
    }

    public function enviarConfirmacionWhatsapp($urlFoto)
    {
        $phone = "573137163216"; // TU NÚMERO
        
        $msg = "✅ *¡PAGO ENVIADO! (Pedido #{$this->order->id})*\n\n";
        $msg .= "💰 *Valor Pagado:* $" . number_format($this->granTotal, 0) . "\n";
        $msg .= "🛵 *Domicilio Incluido:* $" . number_format($this->input_delivery_cost, 0) . "\n\n";
        $msg .= "📎 *MIRA EL COMPROBANTE AQUÍ:* \n";
        $msg .= $urlFoto; // Aquí va el link para que tú le des clic y veas la foto

        $link = "https://wa.me/{$phone}?text=" . urlencode($msg);
        
        return redirect()->away($link);
    }

    public function render()
    {
        return view('livewire.payment-component')->layout('layouts.guest');
    }
}