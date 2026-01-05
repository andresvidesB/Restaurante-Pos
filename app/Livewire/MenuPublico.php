<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class MenuPublico extends Component
{
    // DATOS DE SESIÓN Y CONFIGURACIÓN
    public $carrito = [];
    public $tipoEntrega = 'recogida';
    public $metodo_pago = 'efectivo'; // Default
    public $categoriaSeleccionada = 0;
    
    // ESTADO DE LA INTERFAZ
    public $mostrarCarrito = false; 
    public $pasoCheckout = 1;       

    // DATOS DEL CLIENTE
    public $nombre_cliente;
    public $telefono_cliente;
    public $barrio_cliente;
    public $direccion_cliente;
    public $nota_cliente = ''; // Nueva variable para notas

    public function mount()
    {
        $this->carrito = Session::get('carrito', []);
        $this->tipoEntrega = Session::get('tipoEntrega', 'recogida');
        
        // Si el usuario ya inició sesión, precargamos su nombre
        if(Auth::check()) {
            $this->nombre_cliente = Auth::user()->name;
        }
    }

    public function render()
    {
        $categorias = Categoria::has('productos')->get();
        
        $ofertas = Producto::where('activo', true)
                            ->where('es_oferta', true)
                            ->take(5)->get();

        $query = Producto::where('activo', true);
        
        if ($this->categoriaSeleccionada > 0) {
            $query->where('categoria_id', $this->categoriaSeleccionada);
        }
        
        $productos = $query->orderBy('nombre')->get();

        return view('livewire.menu-publico', compact('categorias', 'productos', 'ofertas'))
                ->layout('layouts.guest'); // Aseguramos el diseño oscuro
    }

    // --- GESTIÓN DEL CARRITO ---

    public function toggleCarrito()
    {
        $this->mostrarCarrito = !$this->mostrarCarrito;
    }

    public function agregarAlCarrito($productoId)
    {
        $producto = Producto::find($productoId);
        if (!$producto) return;
        
        if (isset($this->carrito[$productoId])) {
            $this->carrito[$productoId]['cantidad']++;
        } else {
            // Usamos precio de oferta si existe, sino el normal
            $precio = ($producto->es_oferta && $producto->precio_oferta > 0) 
                      ? $producto->precio_oferta 
                      : $producto->precio;

            $this->carrito[$productoId] = [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => $precio,
                'cantidad' => 1,
                'imagen' => $producto->imagen
            ];
        }
        $this->guardarCarrito();
        $this->mostrarCarrito = true; // Abrir carrito automáticamente al agregar
    }

    public function incrementarCantidad($id)
    {
        if(isset($this->carrito[$id])) {
            $this->carrito[$id]['cantidad']++;
            $this->guardarCarrito();
        }
    }

    public function decrementarCantidad($id)
    {
        if(isset($this->carrito[$id])) {
            if($this->carrito[$id]['cantidad'] > 1) {
                $this->carrito[$id]['cantidad']--;
            } else {
                unset($this->carrito[$id]); 
            }
            $this->guardarCarrito();
        }
    }

    public function eliminarDelCarrito($id)
    {
        unset($this->carrito[$id]);
        $this->guardarCarrito();
    }

    private function guardarCarrito()
    {
        Session::put('carrito', $this->carrito);
    }

    public function getSubtotalProperty()
    {
        return collect($this->carrito)->sum(fn($item) => $item['precio'] * $item['cantidad']);
    }

    // --- PROCESO DE CHECKOUT ---

    public function irAlPaso2()
    {
        if(count($this->carrito) == 0) return;
        $this->pasoCheckout = 2;
    }

    public function volverAlPaso1()
    {
        $this->pasoCheckout = 1;
    }

    public function updatedTipoEntrega($value)
    {
        Session::put('tipoEntrega', $value);
    }

    public function filtrarCategoria($id)
    {
        $this->categoriaSeleccionada = $id;
    }

    public function logout() 
    {
        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();
        return redirect()->route('login');
    }

    // --- FINALIZAR PEDIDO (CREAR ORDEN Y WHATSAPP) ---

    public function finalizarPedido()
    {
        // 1. Validaciones
        $reglas = [
            'nombre_cliente' => 'required|min:3',
            'telefono_cliente' => 'required',
            'metodo_pago' => 'required|in:efectivo,transferencia',
            'nota_cliente' => 'nullable|max:500'
        ];

        if($this->tipoEntrega === 'domicilio') {
            $reglas['barrio_cliente'] = 'required';
            $reglas['direccion_cliente'] = 'required';
        }

        $this->validate($reglas, [
            'required' => 'Campo obligatorio.',
            'min' => 'Muy corto.'
        ]);

        // 2. Crear la Orden en Base de Datos
        // Esto genera el ID necesario para el link de pago
        $order = Order::create([
            'user_id' => Auth::id() ?? null,
            'total' => $this->subtotal,
            'delivery_cost' => 0, // Se definirá por chat o en el paso de pago
            'total_with_delivery' => $this->subtotal, 
            'status' => 'pendiente',
            'notes' => $this->nota_cliente,
            'payment_method' => $this->metodo_pago
        ]);

        // 3. Guardar Detalles de la Orden
        foreach ($this->carrito as $item) {
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'product_name' => $item['nombre'],
                'quantity' => $item['cantidad'],
                'price' => $item['precio']
            ]);
        }

        // 4. Generar Link de Pago (Solo si es transferencia)
        $paymentLink = route('order.pay', $order->id);

        // 5. Construir Mensaje para WhatsApp
        $msg = "🔥 *NUEVO PEDIDO #{$order->id}* 🔥\n\n";
        $msg .= "👤 *Cliente:* {$this->nombre_cliente}\n";
        $msg .= "📱 *Tel:* {$this->telefono_cliente}\n";
        $msg .= "🛵 *Entrega:* " . strtoupper($this->tipoEntrega) . "\n";
        
        if($this->tipoEntrega === 'domicilio') {
            $msg .= "📍 *Dir:* {$this->barrio_cliente} - {$this->direccion_cliente}\n";
        }

        $msg .= "\n--------------------------------\n";
        $msg .= "*DETALLE DEL PEDIDO:*\n";
        foreach ($this->carrito as $item) {
            $subtotalItem = $item['precio'] * $item['cantidad'];
            $msg .= "▪ {$item['cantidad']}x {$item['nombre']} \n";
        }
        $msg .= "--------------------------------\n";

        if($this->nota_cliente){
            $msg .= "📝 *NOTA:* {$this->nota_cliente}\n";
            $msg .= "--------------------------------\n";
        }

        $msg .= "💰 *TOTAL PRODUCTOS: $" . number_format($this->subtotal, 0) . "*\n";
        $msg .= "💳 *PAGO:* " . strtoupper($this->metodo_pago) . "\n";

        if($this->tipoEntrega === 'domicilio') {
            $msg .= "_(Domicilio por confirmar)_\n";
        }

        // Si es transferencia, añadimos el link mágico
        if($this->metodo_pago === 'transferencia') {
            $msg .= "\n👇 *LINK PARA SUBIR COMPROBANTE* 👇\n";
            $msg .= $paymentLink . "\n";
            $msg .= "_(Usa este link cuando sepas el valor del domicilio)_";
        } else {
            $msg .= "\n💵 *Pago contra entrega*";
        }

        // 6. Limpiar y Redirigir
        Session::forget('carrito');
        $this->carrito = [];
        $this->mostrarCarrito = false;
        $this->pasoCheckout = 1;

        $phone = "573137163216"; // TU NÚMERO
        return redirect()->away("https://wa.me/{$phone}?text=" . urlencode($msg));
    }
}