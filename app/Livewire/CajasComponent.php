<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Caja;
use App\Models\Venta;
use App\Models\Gasto; // Asegúrate de tener este modelo o quita la lógica de gastos si no la usas
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CajasComponent extends Component
{
    public $cajaAbierta = null;
    
    // Para Apertura
    public $monto_inicial = 0;

    // Para Cierre
    public $monto_final = 0;
    
    // Totales calculados
    public $totalVentas = 0;
    public $totalGastos = 0;
    public $totalEfectivoEsperado = 0;

    public function mount()
    {
        $this->cargarCaja();
    }

    public function cargarCaja()
    {
        // Buscamos si el usuario tiene caja abierta
        $this->cajaAbierta = Caja::where('user_id', Auth::id())
                                ->whereNull('fecha_cierre')
                                ->first();

        if ($this->cajaAbierta) {
            // Si hay caja, calculamos los totales del día
            $this->calcularTotales();
        }
    }

    public function calcularTotales()
    {
        // Sumar ventas asociadas a este usuario y fecha (o rango de la caja)
        // Aquí simplificamos sumando ventas desde que se abrió la caja
        $this->totalVentas = Venta::where('user_id', Auth::id())
                                  ->where('created_at', '>=', $this->cajaAbierta->fecha_apertura)
                                  ->where('estado', 'Pagado') // Solo lo cobrado
                                  ->sum('total');

        // Si tienes gastos implementados:
        $this->totalGastos = 0; 
        if(class_exists(Gasto::class)) {
             // Asumiendo relación o filtro por caja_id
             // $this->totalGastos = $this->cajaAbierta->gastos->sum('monto');
             // Por ahora lo dejamos en 0 para que no te de error si falta el modelo
        }

        $this->totalEfectivoEsperado = ($this->cajaAbierta->monto_inicial + $this->totalVentas) - $this->totalGastos;
    }

    public function abrirCaja()
    {
        $this->validate(['monto_inicial' => 'required|numeric|min:0']);

        Caja::create([
            'user_id' => Auth::id(),
            'monto_inicial' => $this->monto_inicial,
            'fecha_apertura' => Carbon::now(),
            'estado' => 'Abierta'
        ]);

        return redirect()->route('caja'); // Redirigir al POS al abrir
    }

    public function cerrarCaja()
    {
        $this->validate(['monto_final' => 'required|numeric|min:0']);

        if ($this->cajaAbierta) {
            $this->cajaAbierta->update([
                'fecha_cierre' => Carbon::now(),
                'monto_final' => $this->monto_final,
                'estado' => 'Cerrada'
            ]);
            
            $this->cargarCaja(); // Recargar para mostrar estado cerrado
            session()->flash('mensaje', 'Caja cerrada correctamente.');
        }
    }

    public function render()
    {
        return view('livewire.cajas-component')->layout('layouts.app');
    }
}