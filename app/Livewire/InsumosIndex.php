<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Insumo;
use Livewire\WithPagination;

class InsumosIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    
    // Variables del formulario
    public $insumo_id, $nombre, $unidad_medida, $stock_minimo;
    public $stock_actual = 0; // Iniciamos en 0

    public function render()
    {
        $insumos = Insumo::where('nombre', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.insumos-index', [
            'insumos' => $insumos
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
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
        $this->unidad_medida = 'unidad'; // Valor por defecto
        $this->stock_actual = '';
        $this->stock_minimo = '';
        $this->insumo_id = null;
    }

    public function store()
    {
        // VALIDACIÓN ESTRICTA DE ENTEROS
        $this->validate([
            'nombre' => 'required',
            'unidad_medida' => 'required',
            'stock_actual' => 'required|integer|min:0', // <--- Solo enteros
            'stock_minimo' => 'required|integer|min:1', // <--- Solo enteros
        ]);
        
        Insumo::updateOrCreate(['id' => $this->insumo_id], [
            'nombre' => $this->nombre,
            'unidad_medida' => $this->unidad_medida,
            'stock_actual' => $this->stock_actual,
            'stock_minimo' => $this->stock_minimo
        ]);
        
        session()->flash('mensaje', $this->insumo_id ? 'Insumo actualizado.' : 'Insumo creado exitosamente.');
        
        $this->closeModal();
    }

    public function edit($id)
    {
        $insumo = Insumo::findOrFail($id);
        $this->insumo_id = $id;
        $this->nombre = $insumo->nombre;
        $this->unidad_medida = $insumo->unidad_medida;
        // intval() asegura que al editar no se vean decimales raros
        $this->stock_actual = intval($insumo->stock_actual);
        $this->stock_minimo = intval($insumo->stock_minimo);
    
        $this->openModal();
    }

    public function delete($id)
    {
        Insumo::find($id)->delete();
        session()->flash('mensaje', 'Insumo eliminado.');
    }
}