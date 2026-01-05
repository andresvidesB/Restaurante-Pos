<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Categoria;
use Livewire\WithPagination;

class CategoriasComponent extends Component
{
    use WithPagination;

    public $nombre, $descripcion, $categoria_id;
    public $isModalOpen = false;

    public function render()
    {
        return view('livewire.categorias-component', [
            'categorias' => Categoria::latest()->paginate(10)
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $categoria = Categoria::findOrFail($id);
        $this->categoria_id = $id;
        $this->nombre = $categoria->nombre;
        $this->descripcion = $categoria->descripcion;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'nombre' => 'required|min:3',
        ]);

        Categoria::updateOrCreate(['id' => $this->categoria_id], [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
        ]);

        session()->flash('message', $this->categoria_id ? 'Categoría actualizada.' : 'Categoría creada.');
        $this->closeModal();
    }

    public function delete($id)
    {
        Categoria::find($id)->delete();
        session()->flash('message', 'Categoría eliminada.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->nombre = '';
        $this->descripcion = '';
        $this->categoria_id = null;
    }
}