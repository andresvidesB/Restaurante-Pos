<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Insumo;
use App\Models\Categoria;
use Livewire\WithPagination;
use Livewire\WithFileUploads; // <--- NUEVO: Para subir imágenes
use Illuminate\Support\Facades\Storage; // <--- NUEVO: Para borrar imágenes viejas

class ProductosComponent extends Component
{
    use WithPagination, WithFileUploads; // <--- NUEVO

    public $es_oferta = false;
    // Variables de Vista
    public $search = '';
    public $isOpen = false;
    public $categorias_list;

    // Variables del Formulario
    public $producto_id;
    public $nombre, $precio, $categoria_id;
    
    // --- NUEVAS VARIABLES PARA EL MENÚ CLIENTE ---
    public $imagen; // El archivo temporal
    public $imagen_actual; // Para mostrar la vieja al editar (opcional en lógica, útil en vista)
    public $activo = true; // Por defecto disponible

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
        $this->es_oferta = $producto->es_oferta == 1;
        $this->producto_id = $id;
        $this->nombre = $producto->nombre;
        $this->precio = $producto->precio;
        $this->categoria_id = $producto->categoria_id;
        
        // --- NUEVO: Cargar datos del menú público ---
        $this->activo = $producto->activo == 1; // Convertir a booleano para el checkbox
        $this->imagen_actual = $producto->imagen; // Guardamos la ruta vieja por si no sube nueva

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
        $this->es_oferta = false;
        $this->nombre = '';
        $this->precio = '';
        $this->categoria_id = '';
        $this->producto_id = null;
        
        // --- LIMPIAR NUEVOS CAMPOS ---
        $this->imagen = null;
        $this->imagen_actual = null;
        $this->activo = true;

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
        // Validaciones (Agregué imagen)
        $rules = [
            'nombre' => 'required',
            'precio' => 'required|numeric',
            'categoria_id' => 'required',
            // Cambiamos 'image' por 'file' para evitar el conflicto con AVIF
'imagen' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|max:5000',// Max 5MB
        ];

        $this->validate($rules);

        // Preparamos los datos comunes
        $data = [
            'nombre' => $this->nombre,
            'precio' => $this->precio,
            'categoria_id' => $this->categoria_id,
            'activo' => $this->activo ? 1 : 0, // Guardar booleano
            'es_oferta' => $this->es_oferta ? 1 : 0,
        ];

        // --- LÓGICA DE IMAGEN ---
        if ($this->imagen) {
            // Si estamos editando y suben nueva foto, borramos la vieja (buena práctica)
            if ($this->producto_id) {
                $prodAntiguo = Producto::find($this->producto_id);
                if ($prodAntiguo->imagen) {
                    Storage::disk('public')->delete($prodAntiguo->imagen);
                }
            }
            // Guardar nueva
            $nombreImagen = $this->imagen->store('productos', 'public');
            $data['imagen'] = $nombreImagen;
        }

        if($this->producto_id) {
            // ACTUALIZAR
            $producto = Producto::find($this->producto_id);
            $producto->update($data);
            $mensaje = 'Producto actualizado correctamente.';
        } else {
            // CREAR
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

    public function delete($id)
    {
        $producto = Producto::find($id);
        
        // Borrar imagen del disco si existe
        if($producto->imagen){
            Storage::disk('public')->delete($producto->imagen);
        }

        $producto->insumos()->detach(); 
        $producto->delete();
        
        session()->flash('message', 'Producto eliminado del sistema.');
    }
}