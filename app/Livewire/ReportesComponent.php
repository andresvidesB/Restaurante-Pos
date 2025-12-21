<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class ReportesComponent extends Component
{
    public $tipoReporte = 'dia'; // Opciones: 'dia', 'rango'
    public $fechaInicio;
    public $fechaFin;

    public function mount()
    {
        // Por defecto, la fecha es hoy
        $this->fechaInicio = Carbon::today()->format('Y-m-d');
        $this->fechaFin = Carbon::today()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.reportes-component')->layout('layouts.app');
    }

    // Esta función genera la URL y redirecciona
    public function generarReporte()
    {
        $this->validate([
            'fechaInicio' => 'required|date',
            'fechaFin' => 'required|date|after_or_equal:fechaInicio',
        ]);

        // Redireccionamos al controlador enviando las fechas por URL
        return redirect()->route('reporte.generar', [
            'fecha_inicio' => $this->fechaInicio,
            'fecha_fin' => ($this->tipoReporte == 'rango') ? $this->fechaFin : $this->fechaInicio
        ]);
    }
}