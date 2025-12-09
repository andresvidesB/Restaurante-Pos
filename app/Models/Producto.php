<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    
    protected $fillable = ['nombre', 'precio', 'categoria_id', 'imagen', 'activo'];

    // Relación: Un producto pertenece a una categoría
    public function categoria() {
        return $this->belongsTo(Categoria::class);
    }

    // Relación Mágica: Un producto tiene muchos insumos (Receta)
    public function insumos() {
        return $this->belongsToMany(Insumo::class, 'recetas')
                    ->withPivot('cantidad_requerida');
    }
}