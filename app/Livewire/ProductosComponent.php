<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Insumo;
use App\Models\Categoria;
use Livewire\WithPagination;

class ProductosComponent extends Component
{
    use WithPagination;

    // Variables de Vista
    public $search = '';
    public $isOpen = false;
    public $categorias_list;

    // Variables del Formulario
    public $producto_id; // Para saber si estamos editando
    public $nombre, $precio, $categoria_id;

    // Variables de Receta
    public $insumosDisponibles;
    public $receta = []; // Array temporal
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

    // --- ABRIR PARA CREAR ---
    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    // --- ABRIR PARA EDITAR ---
    public function edit($id)
    {
        $producto = Producto::find($id);
        
        // 1. Llenar datos básicos
        $this->producto_id = $id;
        $this->nombre = $producto->nombre;
        $this->precio = $producto->precio;
        $this->categoria_id = $producto->categoria_id;

        // 2. Reconstruir la receta desde la base de datos
        $this->receta = [];
        foreach($producto->insumos as $insumo) {
            $this->receta[] = [
                'id' => $insumo->id,
                'nombre' => $insumo->nombre,
                'cantidad' => floatval($insumo->pivot->cantidad_requerida), // floatval quita los ceros extra (1.00 -> 1)
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
        $this->receta = [];
        $this->insumoSeleccionado = '';
        $this->cantidadInsumo = '';
    }

    // --- GESTIÓN DE INGREDIENTES LOCALES ---
    public function agregarInsumo()
    {
        if ($this->insumoSeleccionado && $this->cantidadInsumo > 0) {
            $insumoObj = Insumo::find($this->insumoSeleccionado);
            if($insumoObj) {
                // Verificar si ya existe en la lista para no duplicar visualmente
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

    // --- GUARDAR (CREAR O ACTUALIZAR) ---
    public function store()
    {
        $this->validate([
            'nombre' => 'required',
            'precio' => 'required|numeric',
            'categoria_id' => 'required'
        ]);

        // LOGICA DUAL: Si hay ID es actualizar, si no es crear
        if($this->producto_id) {
            // ACTUALIZAR
            $producto = Producto::find($this->producto_id);
            $producto->update([
                'nombre' => $this->nombre,
                'precio' => $this->precio,
                'categoria_id' => $this->categoria_id,
            ]);
            $mensaje = 'Producto actualizado correctamente.';
        } else {
            // CREAR
            $producto = Producto::create([
                'nombre' => $this->nombre,
                'precio' => $this->precio,
                'categoria_id' => $this->categoria_id,
                'activo' => true
            ]);
            $mensaje = 'Producto creado correctamente.';
        }

        // SINCRONIZAR RECETA (Magia de Laravel)
        // sync() borra lo viejo y pone lo nuevo automáticamente
        $datosSync = [];
        foreach ($this->receta as $item) {
            $datosSync[$item['id']] = ['cantidad_requerida' => $item['cantidad']];
        }
        $producto->insumos()->sync($datosSync);

        session()->flash('message', $mensaje);
        $this->closeModal();
    }

    // --- ELIMINAR ---
    public function delete($id)
    {
        $producto = Producto::find($id);
        // Al borrar el producto, la tabla pivote se limpia sola si configuramos cascade, 
        // pero detach() asegura que no queden huérfanos.
        $producto->insumos()->detach(); 
        $producto->delete();
        
        session()->flash('message', 'Producto eliminado del sistema.');
    }
}