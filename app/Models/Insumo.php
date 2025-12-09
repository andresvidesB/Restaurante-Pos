<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'stock_actual', 'stock_minimo', 'unidad_medida'];

    // Para saber en qué productos se usa este insumo
    public function productos() {
        return $this->belongsToMany(Producto::class, 'recetas');
    }
}