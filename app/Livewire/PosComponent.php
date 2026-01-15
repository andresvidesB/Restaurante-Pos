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
use App\Models\Caja;

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
    public $estado_pago = 'Pendiente'; // POR DEFECTO: Pendiente

    // --- VARIABLES NUEVAS PARA PAGO MIXTO ---
    public $pago_efectivo_input = 0;
    public $pago_transferencia_input = 0;
    public $es_pago_mixto = false;

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
        $producto = Producto::with('insumos')->find($productoId);
        
        // --- INICIO BLOQUEO STOCK ---
        // Verificamos si tiene receta y si alcanzan los ingredientes
        if($producto->insumos->count() > 0) {
            foreach($producto->insumos as $insumo) {
                // Cantidad que necesito para 1 producto
                $necesario = $insumo->pivot->cantidad_requerida;
                
                // Si ya tengo este producto en el carrito, sumo lo que ya llevo
                $enCarrito = isset($this->carrito[$productoId]) ? $this->carrito[$productoId]['cantidad'] : 0;
                $totalNecesario = ($enCarrito + 1) * $necesario;

                if($insumo->stock_actual < $totalNecesario) {
                    // SI FALTA STOCK: Mandamos error y PARAMOS TODO
                    session()->flash('mensaje', "🚫 NO HAY STOCK: Falta '{$insumo->nombre}'. Stock actual: {$insumo->stock_actual}");
                    return; 
                }
            }
        }
        // --- FIN BLOQUEO STOCK ---

        // Si pasa la validación, sigue el código normal...
        if(isset($this->carrito[$productoId])) {
            $this->carrito[$productoId]['cantidad']++;
        } else {
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
        
        // ACTUALIZAR INPUTS DE PAGO AUTOMÁTICAMENTE
        $this->actualizarInputsPago();
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

    // --- LOGICA DE PAGOS MIXTOS ---
    
    public function updatedMetodoPago($value)
    {
        if($value == 'Mixto') {
            $this->es_pago_mixto = true;
            // Inicializamos en 0 para que el usuario escriba
            $this->pago_efectivo_input = 0;
            $this->pago_transferencia_input = 0;
        } else {
            $this->es_pago_mixto = false;
            $this->actualizarInputsPago();
        }
    }

    public function actualizarInputsPago()
    {
        // Si no es mixto, llenamos automáticamente el input correspondiente con el total
        if($this->metodo_pago == 'Efectivo') {
            $this->pago_efectivo_input = $this->total;
            $this->pago_transferencia_input = 0;
        } elseif ($this->metodo_pago == 'Transferencia' || $this->metodo_pago == 'Nequi/Daviplata' || $this->metodo_pago == 'Tarjeta') {
            $this->pago_efectivo_input = 0;
            $this->pago_transferencia_input = $this->total;
        }
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
        
        // Resetear pagos mixtos
        $this->pago_efectivo_input = 0;
        $this->pago_transferencia_input = 0;
        $this->es_pago_mixto = false;
        $this->metodo_pago = 'Efectivo';
    }

    // --- COBRAR (PROCESO BLINDADO) ---
    public function cobrar()
    {
        // 1. CANDADO DE CAJA (SEGURIDAD)
        // Verificamos si el usuario tiene una caja abierta
        $cajaAbierta = Caja::where('user_id', auth()->id())
                            ->whereNull('fecha_cierre')
                            ->exists();

        if (!$cajaAbierta) {
            // Si no hay caja, lanzamos el error y DETENEMOS la función
            session()->flash('mensaje', '⛔ CAJA CERRADA: Debes abrir caja para realizar ventas.');
            return;
        }

        // 2. Validar que haya productos
        if(empty($this->carrito)) return;

        // 3. Validaciones según el tipo de servicio
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

        // 4. Validación de Pago Mixto
        $totalPagado = $this->pago_efectivo_input + $this->pago_transferencia_input;
        
        // Usamos abs() para evitar problemas mínimos de redondeo con decimales flotantes
        if(abs($totalPagado - $this->total) > 0.1) {
            session()->flash('mensaje', '❌ Error: La suma de pagos (' . number_format($totalPagado) . ') no coincide con el Total (' . number_format($this->total) . ')');
            return;
        }

        // 5. Transacción Segura (Try-Catch)
       // try {
            DB::transaction(function () {
                
                // A. Crear la Venta (Cabecera)
                $venta = Venta::create([
                    'total' => $this->total,
                    'pago_efectivo' => $this->pago_efectivo_input,
                    'pago_transferencia' => $this->pago_transferencia_input,
                    'metodo_pago' => $this->es_pago_mixto ? 'Mixto' : $this->metodo_pago,
                    'costo_envio' => ($this->tipo_servicio == 'Domicilio') ? $this->costo_envio : 0,
                    'estado' => $this->estado_pago,
                    'tipo_servicio' => $this->tipo_servicio,
                    'numero_mesa' => ($this->tipo_servicio == 'Mesa') ? $this->numero_mesa : null,
                    
                    // Guardar nombre si es Domicilio O Mostrador
                    'cliente_nombre' => ($this->tipo_servicio == 'Domicilio' || $this->tipo_servicio == 'Mostrador') ? $this->cliente_nombre : null,
                    
                    'cliente_telefono' => ($this->tipo_servicio == 'Domicilio') ? $this->cliente_telefono : null,
                    'cliente_direccion' => ($this->tipo_servicio == 'Domicilio') ? $this->cliente_direccion : null,
                    'user_id' => auth()->id() ?? 1,
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

      //  } catch (\Exception $e) {
            // Si algo falla:
      //      Log::error("Error en Venta POS: " . $e->getMessage());
     //       session()->flash('mensaje', 'Error al procesar la venta. Verifique los datos o intente nuevamente.');
       // }
    }
}