<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Insumo;
use App\Models\Categoria;
use App\Models\DetalleVenta; // <--- VITAL: Para consultar el historial
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class ProductosComponent extends Component
{
    use WithPagination, WithFileUploads;

    // --- VARIABLES DE OFERTA ---
    public $es_oferta = false;
    public $precio_oferta; 

    // --- VARIABLES DE VISTA ---
    public $search = '';
    public $isOpen = false;
    public $categorias_list;

    // --- VARIABLES DEL FORMULARIO ---
    public $producto_id;
    public $nombre, $precio, $categoria_id;
    
    // --- VARIABLES MENÚ PÚBLICO ---
    public $imagen;        // Archivo temporal al subir
    public $imagen_actual; // Ruta guardada (para mostrar vista previa)
    public $activo = true; 

    // --- VARIABLES DE RECETA ---
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
        // Mostramos productos (incluso los inactivos, para poder reactivarlos si quieres)
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
        
        // Cargar datos básicos
        $this->producto_id = $id;
        $this->nombre = $producto->nombre;
        $this->precio = $producto->precio;
        $this->categoria_id = $producto->categoria_id;
        
        // Cargar datos extra
        $this->es_oferta = $producto->es_oferta == 1;
        $this->precio_oferta = $producto->precio_oferta;
        $this->activo = $producto->activo == 1;
        $this->imagen_actual = $producto->imagen;

        // Cargar Receta
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
        $this->es_oferta = false;
        $this->precio_oferta = null;
        $this->imagen = null;
        $this->imagen_actual = null;
        $this->activo = true;
        $this->receta = [];
        $this->insumoSeleccionado = '';
        $this->cantidadInsumo = '';
    }

    // --- LÓGICA DE INSUMOS ---
    public function agregarInsumo()
    {
        if ($this->insumoSeleccionado && $this->cantidadInsumo > 0) {
            $insumoObj = Insumo::find($this->insumoSeleccionado);
            if($insumoObj) {
                // Evitar duplicados visuales
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

    // --- GUARDAR ---
    public function store()
    {
        $rules = [
            'nombre' => 'required',
            'precio' => 'required|numeric',
            'categoria_id' => 'required',
            'imagen' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|max:5000', // Max 5MB
            'precio_oferta' => $this->es_oferta ? 'required|numeric' : 'nullable',
        ];

        $this->validate($rules);

        $data = [
            'nombre' => $this->nombre,
            'precio' => $this->precio,
            'categoria_id' => $this->categoria_id,
            'activo' => $this->activo ? 1 : 0,
            'es_oferta' => $this->es_oferta ? 1 : 0,
            'precio_oferta' => $this->es_oferta ? $this->precio_oferta : null,
        ];

        // Subir Imagen
        if ($this->imagen) {
            // Si editamos, borramos la vieja para no llenar el servidor de basura
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

        // Guardar Receta
        $datosSync = [];
        foreach ($this->receta as $item) {
            $datosSync[$item['id']] = ['cantidad_requerida' => $item['cantidad']];
        }
        $producto->insumos()->sync($datosSync);

        session()->flash('message', $mensaje);
        $this->closeModal();
    }

    // --- BORRADO INTELIGENTE (TU SOLUCIÓN) ---
    public function delete($id)
    {
        $producto = Producto::find($id);
        
        if(!$producto) return;

        // 1. Preguntamos: ¿Este producto se ha vendido alguna vez?
        $tieneVentas = DetalleVenta::where('producto_id', $id)->exists();

        if ($tieneVentas) {
            // CASO A: SÍ TIENE VENTAS
            // No lo borramos. Solo lo ocultamos.
            // Así el PDF histórico lo encuentra, pero el menú nuevo no.
            $producto->update(['activo' => false]);
            
            session()->flash('message', '⚠️ El producto tiene historial de ventas. Se ha DESACTIVADO (Oculto) para no romper las facturas antiguas.');
        } else {
            // CASO B: ES NUEVO (Nunca se vendió)
            // Borramos todo sin miedo.
            
            if($producto->imagen){
                Storage::disk('public')->delete($producto->imagen);
            }

            $producto->insumos()->detach(); // Borrar receta
            $producto->delete(); // Borrar producto
            
            session()->flash('message', 'Producto eliminado definitivamente del sistema.');
        }
    }
}