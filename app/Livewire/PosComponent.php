<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Importante para registrar errores internos
use Illuminate\Support\Str;

class PosComponent extends Component
{
    // Variables de Vista
    public $categorias;
    public $categoriaSeleccionada = null;
    public $search = '';
    
    // Carrito de Compras
    public $carrito = [];
    public $total = 0;
    public $articulosCount = 0;

    // --- VARIABLES DE SERVICIO (Mesa / Domicilio) ---
    public $tipo_servicio = 'Mostrador'; // Opciones: Mostrador, Mesa, Domicilio
    public $numero_mesa = null;
    
    // Datos del Cliente (Domicilio)
    public $cliente_nombre = '';
    public $cliente_telefono = '';
    public $cliente_direccion = '';
    public $costo_envio = 0;
    
    // Pago
    public $metodo_pago = 'Efectivo';
    public $estado_pago = 'Pendiente'; // POR DEFECTO: Pendiente (Como lo pediste)

    public function mount()
    {
        $this->categorias = Categoria::all();
    }

    public function render()
    {
        $productos = Producto::query()
            ->when($this->categoriaSeleccionada, function($query) {
                return $query->where('categoria_id', $this->categoriaSeleccionada);
            })
            ->when($this->search, function($query) {
                return $query->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->where('activo', true)
            ->get();

        return view('livewire.pos-component', [
            'productos' => $productos
        ])->layout('layouts.app');
    }

    // --- LOGICA CARRITO ---
    public function addToCart($productoId)
    {
        $producto = Producto::find($productoId);
        
        if(isset($this->carrito[$productoId])) {
            $this->carrito[$productoId]['cantidad']++;
        } else {
            // Agregamos al carrito con datos visuales
            $this->carrito[$productoId] = [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => $producto->precio,
                'cantidad' => 1,
                'imagen' => $producto->imagen,
                'categoria' => $producto->categoria->nombre ?? '',
                'observacion' => ''
            ];
        }
        $this->calcularTotal();
    }

    public function increment($id) { 
        $this->carrito[$id]['cantidad']++; 
        $this->calcularTotal(); 
    }

    public function decrement($id) { 
        if ($this->carrito[$id]['cantidad'] > 1) { 
            $this->carrito[$id]['cantidad']--; 
        } else { 
            unset($this->carrito[$id]); 
        }
        $this->calcularTotal(); 
    }

    public function removeItem($id) { 
        unset($this->carrito[$id]); 
        $this->calcularTotal(); 
    }

    public function calcularTotal()
    {
        $subtotal = 0;
        $this->articulosCount = 0;
        
        foreach($this->carrito as $item) {
            $subtotal += $item['precio'] * $item['cantidad'];
            $this->articulosCount += $item['cantidad'];
        }
        
        // Sumar costo de envío solo si es Domicilio
        $envio = ($this->tipo_servicio == 'Domicilio') ? floatval($this->costo_envio) : 0;
        
        $this->total = $subtotal + $envio;
    }

    // --- LOGICA DE SERVICIOS ---
    public function updatedTipoServicio($value)
    {
        $this->calcularTotal(); // Recalcular total (por si quita/pone envío)
        
        // SIEMPRE resetear a Pendiente al cambiar de pestaña
        $this->estado_pago = 'Pendiente'; 

        // Si no es domicilio, quitamos el costo de envío
        if($value != 'Domicilio') {
            $this->costo_envio = 0;
        }
    }
    
    // Si cambia el costo de envío manualmente, recalcular total
    public function updatedCostoEnvio() { 
        $this->calcularTotal(); 
    }

    // --- LIMPIAR TODO ---
    public function cancelarVenta()
    {
        $this->carrito = [];
        $this->total = 0;
        $this->articulosCount = 0;
        $this->reset(['cliente_nombre', 'cliente_telefono', 'cliente_direccion', 'numero_mesa', 'costo_envio']);
        
        $this->tipo_servicio = 'Mostrador';
        $this->estado_pago = 'Pendiente'; // Resetear siempre a Pendiente
    }

    // --- COBRAR (PROCESO BLINDADO) ---
    public function cobrar()
    {
        if(empty($this->carrito)) return;

        // 1. Validaciones según el tipo de servicio
        if($this->tipo_servicio == 'Domicilio') {
            $this->validate([
                'cliente_nombre' => 'required',
                'cliente_direccion' => 'required',
                'cliente_telefono' => 'required'
            ]);
        }
        
        if($this->tipo_servicio == 'Mesa') {
            $this->validate(['numero_mesa' => 'required|integer']);
        }

        // 2. Transacción Segura (Try-Catch)
        try {
            DB::transaction(function () {
                
                // A. Crear la Venta (Cabecera)
                $venta = Venta::create([
                    'total' => $this->total,
                    'costo_envio' => ($this->tipo_servicio == 'Domicilio') ? $this->costo_envio : 0,
                    'metodo_pago' => $this->metodo_pago,
                    'estado' => $this->estado_pago,
                    'tipo_servicio' => $this->tipo_servicio,
                    'numero_mesa' => ($this->tipo_servicio == 'Mesa') ? $this->numero_mesa : null,
                    'cliente_nombre' => ($this->tipo_servicio == 'Domicilio') ? $this->cliente_nombre : null,
                    'cliente_telefono' => ($this->tipo_servicio == 'Domicilio') ? $this->cliente_telefono : null,
                    'cliente_direccion' => ($this->tipo_servicio == 'Domicilio') ? $this->cliente_direccion : null,
                    'user_id' => auth()->id() ?? 1, // Usuario actual o ID 1 por defecto
                    'codigo_factura' => 'FAC-' . time()
                ]);

                // B. Crear Detalles y Descontar Inventario
                foreach ($this->carrito as $item) {
                    
                    // Guardar detalle
                    DetalleVenta::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $item['id'],
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio'],
                        'subtotal' => $item['precio'] * $item['cantidad'],
                        'observacion' => $item['observacion'] ?? null
                    ]);

                    // Descontar Stock (Receta)
                    $productoModel = Producto::find($item['id']);
                    
                    if($productoModel && $productoModel->insumos->count() > 0) {
                        foreach ($productoModel->insumos as $insumo) {
                            $cantidadDescontar = $item['cantidad'] * $insumo->pivot->cantidad_requerida;
                            $insumo->decrement('stock_actual', $cantidadDescontar);
                        }
                    }
                }
            });

            // Si todo sale bien:
            $this->cancelarVenta();
            session()->flash('mensaje', '¡Venta registrada exitosamente!');

        } catch (\Exception $e) {
            // Si algo falla:
            // 1. Guardamos el error real en los logs (para ti como desarrollador)
            Log::error("Error en Venta POS: " . $e->getMessage());

            // 2. Mostramos un mensaje amable al usuario
            session()->flash('mensaje', 'Error al procesar la venta. Verifique los datos o intente nuevamente.');
        }
    }
}