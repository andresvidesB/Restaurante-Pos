<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use Livewire\WithPagination;

class OfertasComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $isModalOpen = false;

    // Variables del formulario
    public $producto_seleccionado_id;
    public $nombre_producto;
    public $precio_original;
    public $precio_oferta;

    // Buscador interno del modal
    public $searchProduct = ''; 

    public function render()
    {
        // 1. Cargar ofertas actuales
        $ofertas = Producto::where('es_oferta', true)
            ->where('nombre', 'like', '%' . $this->search . '%')
            ->orderBy('updated_at', 'desc') // Las más recientes primero
            ->paginate(10);

        // 2. Buscar productos para NUEVA oferta (que NO sean oferta aún)
        $productosDisponibles = [];
        if($this->isModalOpen && !$this->producto_seleccionado_id) {
            $productosDisponibles = Producto::where('es_oferta', false)
                ->where('nombre', 'like', '%' . $this->searchProduct . '%')
                ->take(5)
                ->get();
        }

        return view('livewire.ofertas-component', [
            'ofertas' => $ofertas,
            'productosDisponibles' => $productosDisponibles
        ])->layout('layouts.app');
    }

    // ABRIR MODAL PARA CREAR DESDE CERO
    public function create()
    {
        $this->reset(['producto_seleccionado_id', 'precio_original', 'precio_oferta', 'nombre_producto', 'searchProduct']);
        $this->isModalOpen = true;
    }

    // ABRIR MODAL PARA EDITAR UNA OFERTA EXISTENTE (CORREGIR PRECIO 0)
    public function edit($id)
    {
        $prod = Producto::find($id);
        $this->producto_seleccionado_id = $prod->id;
        $this->nombre_producto = $prod->nombre;
        $this->precio_original = $prod->precio;
        
        // Si el precio oferta es 0 o nulo, dejamos el campo vacío para que lo llenes
        $this->precio_oferta = ($prod->precio_oferta > 0) ? $prod->precio_oferta : ''; 
        
        $this->isModalOpen = true;
    }

    // SELECCIONAR PRODUCTO DE LA LISTA DE BÚSQUEDA
    public function seleccionarProducto($id)
    {
        $prod = Producto::find($id);
        $this->producto_seleccionado_id = $prod->id;
        $this->nombre_producto = $prod->nombre;
        $this->precio_original = $prod->precio;
        $this->precio_oferta = ''; 
    }

    // GUARDAR CAMBIOS
    public function store()
    {
        $this->validate([
            'producto_seleccionado_id' => 'required',
            'precio_oferta' => 'required|numeric|min:1|lt:precio_original', // Debe ser mayor a 1 y menor al original
        ], [
            'precio_oferta.lt' => 'El precio de oferta debe ser menor al real.',
            'precio_oferta.min' => 'El precio no puede ser cero.'
        ]);

        $producto = Producto::find($this->producto_seleccionado_id);
        $producto->update([
            'es_oferta' => true,
            'precio_oferta' => $this->precio_oferta
        ]);

        session()->flash('message', '¡Precio de oferta actualizado correctamente!');
        $this->isModalOpen = false;
    }

    // QUITAR OFERTA
    public function quitarOferta($id)
    {
        $producto = Producto::find($id);
        $producto->update([
            'es_oferta' => false,
            'precio_oferta' => null
        ]);
        
        session()->flash('message', 'El producto ya no está en oferta.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }
}