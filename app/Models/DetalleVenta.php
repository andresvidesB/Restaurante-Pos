<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    // Relación 1: Este detalle pertenece a una Venta
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    // Relación 2: Este detalle pertenece a un Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}