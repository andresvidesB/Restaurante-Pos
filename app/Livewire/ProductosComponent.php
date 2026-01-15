<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Insumo;
use App\Models\Categoria;
use App\Models\DetalleVenta; // <--- IMPRESCINDIBLE PARA EL ERROR DE BORRADO
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class ProductosComponent extends Component
{
    use WithPagination, WithFileUploads;

    // Variables de Oferta
    public $es_oferta = false;
    public $precio_oferta; // <--- FALTABA ESTA VARIABLE

    // Variables de Vista
    public $search = '';
    public $isOpen = false;
    public $categorias_list;

    // Variables del Formulario
    public $producto_id;
    public $nombre, $precio, $categoria_id;
    
    // Variables Menú Cliente
    public $imagen; 
    public $imagen_actual; 
    public $activo = true; 

    // Variables de Receta
    public $insumosDisponibles;
    public $receta = []; 
    public $insumoSeleccionado, $cantidadInsumo;

    public function mount()
    {
        $this->categorias_list = Categoria::all();
        $this->insumosDisponibles = Insumo::all();
    }

    public function render()
    {
        $productos = Producto::where('nombre', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->with(['categoria', 'insumos'])
            ->paginate(9);

        return view('livewire.productos-component', [
            'productos' => $productos
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $producto = Producto::find($id);
        
        // 1. Llenar datos básicos
        $this->producto_id = $id;
        $this->nombre = $producto->nombre;
        $this->precio = $producto->precio;
        $this->categoria_id = $producto->categoria_id;
        
        // Datos de Oferta
        $this->es_oferta = $producto->es_oferta == 1;
        $this->precio_oferta = $producto->precio_oferta; // <--- CARGAR PRECIO OFERTA
        
        // Datos Menú Público
        $this->activo = $producto->activo == 1; 
        $this->imagen_actual = $producto->imagen; 

        // 2. Reconstruir la receta
        $this->receta = [];
        foreach($producto->insumos as $insumo) {
            $this->receta[] = [
                'id' => $insumo->id,
                'nombre' => $insumo->nombre,
                'cantidad' => floatval($insumo->pivot->cantidad_requerida),
                'unidad' => $insumo->unidad_medida
            ];
        }

        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->nombre = '';
        $this->precio = '';
        $this->categoria_id = '';
        $this->producto_id = null;
        
        // Limpiar Ofertas
        $this->es_oferta = false;
        $this->precio_oferta = null; // <--- RESETEAR
        
        // Limpiar Imagen
        $this->imagen = null;
        $this->imagen_actual = null;
        $this->activo = true;

        // Limpiar Receta
        $this->receta = [];
        $this->insumoSeleccionado = '';
        $this->cantidadInsumo = '';
    }

    public function agregarInsumo()
    {
        if ($this->insumoSeleccionado && $this->cantidadInsumo > 0) {
            $insumoObj = Insumo::find($this->insumoSeleccionado);
            if($insumoObj) {
                foreach($this->receta as $item) {
                    if($item['id'] == $insumoObj->id) return; 
                }

                $this->receta[] = [
                    'id' => $insumoObj->id,
                    'nombre' => $insumoObj->nombre,
                    'cantidad' => $this->cantidadInsumo,
                    'unidad' => $insumoObj->unidad_medida
                ];
            }
            $this->insumoSeleccionado = '';
            $this->cantidadInsumo = '';
        }
    }

    public function quitarInsumo($index)
    {
        unset($this->receta[$index]);
        $this->receta = array_values($this->receta);
    }

    public function store()
    {
        // Validaciones
        $rules = [
            'nombre' => 'required',
            'precio' => 'required|numeric',
            'categoria_id' => 'required',
            'imagen' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|max:5000',
            // Si es oferta, el precio oferta es obligatorio y debe ser numérico
            'precio_oferta' => $this->es_oferta ? 'required|numeric' : 'nullable',
        ];

        $this->validate($rules);

        // Datos a guardar
        $data = [
            'nombre' => $this->nombre,
            'precio' => $this->precio,
            'categoria_id' => $this->categoria_id,
            'activo' => $this->activo ? 1 : 0,
            
            // Lógica Oferta
            'es_oferta' => $this->es_oferta ? 1 : 0,
            'precio_oferta' => $this->es_oferta ? $this->precio_oferta : null, // Guardar o anular
        ];

        // --- LÓGICA DE IMAGEN ---
        if ($this->imagen) {
            if ($this->producto_id) {
                $prodAntiguo = Producto::find($this->producto_id);
                if ($prodAntiguo && $prodAntiguo->imagen) {
                    Storage::disk('public')->delete($prodAntiguo->imagen);
                }
            }
            $nombreImagen = $this->imagen->store('productos', 'public');
            $data['imagen'] = $nombreImagen;
        }

        if($this->producto_id) {
            $producto = Producto::find($this->producto_id);
            $producto->update($data);
            $mensaje = 'Producto actualizado correctamente.';
        } else {
            $producto = Producto::create($data);
            $mensaje = 'Producto creado correctamente.';
        }

        // SINCRONIZAR RECETA
        $datosSync = [];
        foreach ($this->receta as $item) {
            $datosSync[$item['id']] = ['cantidad_requerida' => $item['cantidad']];
        }
        $producto->insumos()->sync($datosSync);

        session()->flash('message', $mensaje);
        $this->closeModal();
    }

    // --- FUNCIÓN DELETE CORREGIDA (EVITA EL ERROR SQL) ---
    public function delete($id)
    {
        $producto = Producto::find($id);
        
        if(!$producto) return;

        // 1. Verificar si tiene ventas asociadas
        $tieneVentas = DetalleVenta::where('producto_id', $id)->exists();

        if ($tieneVentas) {
            // OPCIÓN A: TIENE HISTORIAL -> SOLO DESACTIVAR
            $producto->update(['activo' => false]);
            session()->flash('message', '⚠️ El producto tiene ventas registradas. Se ha DESACTIVADO para no romper el historial.');
        } else {
            // OPCIÓN B: ES NUEVO -> BORRAR DE VERDAD
            
            // Borrar imagen si existe
            if($producto->imagen){
                Storage::disk('public')->delete($producto->imagen);
            }

            // Borrar receta y producto
            $producto->insumos()->detach(); 
            $producto->delete();
            
            session()->flash('message', 'Producto eliminado definitivamente del sistema.');
        }
    }
}