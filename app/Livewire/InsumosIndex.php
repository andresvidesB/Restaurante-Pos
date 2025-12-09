<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Insumo;
use Livewire\WithPagination;

class InsumosIndex extends Component
{
    use WithPagination;

    public $search = '';
    
    // Variables para el formulario de crear/editar
    public $nombre, $stock_actual, $stock_minimo, $unidad_medida = 'unidad';
    public $isOpen = false; // Para abrir/cerrar el modal

    public function render()
    {
        $insumos = Insumo::where('nombre', 'like', '%'.$this->search.'%')
                        ->orderBy('stock_actual', 'asc')
                        ->paginate(10);

        return view('livewire.insumos-index', ['insumos' => $insumos])->layout('layouts.app');
    }

    // Abrir Modal
    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    // Cerrar Modal
    public function closeModal()
    {
        $this->isOpen = false;
    }

    // Limpiar campos
    private function resetInputFields()
    {
        $this->nombre = '';
        $this->stock_actual = '';
        $this->stock_minimo = '';
        $this->unidad_medida = 'unidad';
    }

    // GUARDAR EN BD
    public function store()
    {
        $this->validate([
            'nombre' => 'required',
            'stock_actual' => 'required|numeric',
            'stock_minimo' => 'required|numeric',
            'unidad_medida' => 'required',
        ]);

        Insumo::create([
            'nombre' => $this->nombre,
            'stock_actual' => $this->stock_actual,
            'stock_minimo' => $this->stock_minimo,
            'unidad_medida' => $this->unidad_medida,
        ]);

        session()->flash('message', 'Insumo Creado Exitosamente.');
        $this->closeModal();
        $this->resetInputFields();
    }
}