<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo_factura', 
        'total', 
    'costo_envio',    // Nuevo
    'metodo_pago', 
    'estado', 
    'user_id',
    'tipo_servicio',  // Nuevo
    'numero_mesa',    // Nuevo
    'cliente_nombre', // Nuevo
    'cliente_telefono',// Nuevo
    'cliente_direccion'// Nuevo
    ];

    // Relación: Una venta tiene muchos detalles
    public function detalles() {
        return $this->hasMany(DetalleVenta::class);
    }
    
    // Relación: Una venta pertenece a un Cajero (Usuario)
    public function cajero() {
        return $this->belongsTo(User::class, 'user_id');
    }
}