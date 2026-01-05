<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Categoria;
use App\Models\Producto;

class MenuPublico extends Component
{
    // DATOS DE SESIÓN Y CONFIGURACIÓN
    public $carrito = [];
    public $tipoEntrega = 'recogida';
    public $categoriaSeleccionada = 0;
    
    // ESTADO DE LA INTERFAZ
    public $mostrarCarrito = false; // Para minimizar/maximizar
    public $pasoCheckout = 1;       // 1 = Revisar Carrito, 2 = Datos Cliente

    // DATOS DEL CLIENTE
    public $nombre_cliente;
    public $telefono_cliente;
    public $barrio_cliente;
    public $direccion_cliente;

    public function mount()
    {
        $this->carrito = session()->get('carrito', []);
        $this->tipoEntrega = session()->get('tipoEntrega', 'recogida');
        
        // Si hay items, mostramos el carrito al entrar (opcional)
        if(count($this->carrito) > 0) {
            $this->mostrarCarrito = true;
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
                ->layout('layouts.guest'); 
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
            $precio = ($producto->es_oferta && $producto->precio_oferta) ? $producto->precio_oferta : $producto->precio;
            $this->carrito[$productoId] = [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => $precio,
                'cantidad' => 1,
                'imagen' => $producto->imagen
            ];
        }
        $this->guardarCarrito();
        $this->mostrarCarrito = true; // Abrir carrito al agregar
        session()->flash('mensaje', '¡Agregado!');
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
                unset($this->carrito[$id]); // Si baja de 1, se elimina
            }
            $this->guardarCarrito();
        }
    }

    public function eliminarDelCarrito($id)
    {
        unset($this->carrito[$id]);
        $this->guardarCarrito();
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
        session()->put('tipoEntrega', $value);
    }

    public function finalizarPedido()
    {
        // 1. Validación de campos
        $reglas = [
            'nombre_cliente' => 'required|min:3',
            'telefono_cliente' => 'required|min:7',
        ];

        if($this->tipoEntrega === 'domicilio') {
            $reglas['barrio_cliente'] = 'required';
            $reglas['direccion_cliente'] = 'required';
        }

        $this->validate($reglas, [
            'required' => 'Este campo es obligatorio.',
            'min' => 'Muy corto.'
        ]);

        // 2. Construir Mensaje para WhatsApp
        $mensaje = "*¡HOLA! QUIERO REALIZAR UN PEDIDO WEB* 🔥\n\n";
        $mensaje .= "*Nombre:* " . $this->nombre_cliente . "\n";
        $mensaje .= "*Teléfono:* " . $this->telefono_cliente . "\n";
        $mensaje .= "*Tipo:* " . strtoupper($this->tipoEntrega) . "\n";
        
        if($this->tipoEntrega === 'domicilio') {
            $mensaje .= "*Dirección:* " . $this->direccion_cliente . "\n";
            $mensaje .= "*Barrio:* " . $this->barrio_cliente . "\n";
        }

        $mensaje .= "\n--------------------------------\n";
        $mensaje .= "*DETALLE DEL PEDIDO:*\n";
        
        foreach($this->carrito as $item) {
            $subtotal = $item['precio'] * $item['cantidad'];
            $mensaje .= "• (" . $item['cantidad'] . "x) " . $item['nombre'] . " - $" . number_format($subtotal, 0) . "\n";
        }

        $mensaje .= "--------------------------------\n";
        $mensaje .= "*TOTAL PRODUCTOS:* $" . number_format($this->subtotal, 0) . "\n";
        
        if($this->tipoEntrega === 'domicilio') {
            $mensaje .= "\n⚠️ *Nota:* Quedo atento al valor del domicilio para confirmar el pago total.";
        }

        // 3. Redirigir a WhatsApp
        $url = "https://wa.me/573137163216?text=" . urlencode($mensaje);
        
        // Limpiar carrito (opcional, o dejarlo hasta que confirmen)
        // $this->reset(['carrito', 'pasoCheckout', 'nombre_cliente', ...]);
        // session()->forget('carrito');

        return redirect()->away($url);
    }

    // --- UTILIDADES ---

    public function getSubtotalProperty()
    {
        return collect($this->carrito)->sum(fn($item) => $item['precio'] * $item['cantidad']);
    }

    private function guardarCarrito()
    {
        session()->put('carrito', $this->carrito);
    }

    public function filtrarCategoria($id)
    {
        $this->categoriaSeleccionada = $id;
    }

    public function logout() {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('home');
    }
}