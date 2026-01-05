<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Categoria; // <--- ESTA LÍNEA ES LA CLAVE (Importar el Modelo)
use App\Models\Producto;

class MenuPublico extends Component
{
    public $categoriaSeleccionada = 0; // Variable pública para el filtro

    public function render()
{
    $categorias = Categoria::has('productos')->get();
    
    // Obtener ofertas especiales (activos y marcados como oferta)
    $ofertas = Producto::where('activo', true)
                        ->where('es_oferta', true)
                        ->take(5) // Máximo 5 ofertas para el banner
                        ->get();

    $query = Producto::where('activo', true);
    if ($this->categoriaSeleccionada > 0) {
        $query->where('categoria_id', $this->categoriaSeleccionada);
    }
    $productos = $query->orderBy('nombre')->get();

    return view('livewire.menu-publico', [
        'categorias' => $categorias,
        'productos' => $productos,
        'ofertas' => $ofertas, // <--- Enviamos las ofertas
        'categoriaSeleccionada' => $this->categoriaSeleccionada
    ])->layout('layouts.guest'); 
}
    public function filtrarCategoria($id)
    {
        $this->categoriaSeleccionada = $id;
    }

    public function logout() {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('home');
}
}