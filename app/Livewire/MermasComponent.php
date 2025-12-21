<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Insumo;
use App\Models\Merma;
use Livewire\WithPagination;
use Carbon\Carbon;

class MermasComponent extends Component
{
    use WithPagination;

    public $insumo_id, $cantidad, $motivo;
    public $search = '';

    public function render()
    {
        // Listamos el historial de mermas
        $mermas = Merma::whereHas('insumo', function($q){
                        $q->where('nombre', 'like', '%'.$this->search.'%');
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        // Listamos insumos para el select del formulario
        $insumos = Insumo::orderBy('nombre')->get();

        return view('livewire.mermas-component', [
            'mermas' => $mermas,
            'insumos' => $insumos
        ])->layout('layouts.app');
    }

    public function guardarMerma()
    {
        $this->validate([
            'insumo_id' => 'required',
            'cantidad' => 'required|numeric|min:1', // Enteros positivos
            'motivo' => 'required|min:3'
        ]);

        $insumo = Insumo::find($this->insumo_id);

        // 1. Verificar si hay stock suficiente para dar de baja
        if($insumo->stock_actual < $this->cantidad) {
            session()->flash('error', 'No puedes dar de baja más cantidad de la que existe en inventario.');
            return;
        }

        // 2. Registrar la Merma
        Merma::create([
            'insumo_id' => $this->insumo_id,
            'cantidad' => $this->cantidad,
            'motivo' => $this->motivo,
            'fecha' => Carbon::now(),
            'user_id' => auth()->id() ?? 1
        ]);

        // 3. Descontar del Stock
        $insumo->decrement('stock_actual', $this->cantidad);

        // 4. Limpiar
        $this->reset(['insumo_id', 'cantidad', 'motivo']);
        session()->flash('mensaje', 'Merma registrada y stock descontado correctamente.');
    }
}