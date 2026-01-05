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
        'costo_envio',
        'metodo_pago', 
        'estado', 
        'user_id',
        'tipo_servicio',
        'numero_mesa',
        'cliente_nombre',
        'cliente_telefono',
        'cliente_direccion',
        
        // --- CAMPOS NUEVOS AGREGADOS ---
        'pago_efectivo',       // Faltaba este
        'pago_transferencia',  // Faltaba este
        'motivo_anulacion'     // Faltaba este
    ];

    // Relación: Una venta tiene muchos detalles
    public function detalles() {
        return $this->hasMany(DetalleVenta::class);
    }
    
    // Relación ORIGINAL (se mantiene para que no falle lo anterior)
    public function cajero() {
        return $this->belongsTo(User::class, 'user_id');
    }

    // --- SOLUCIÓN AL ERROR ---
    // Agregamos este alias "user" que hace lo mismo que "cajero".
    // Así funciona tanto $venta->cajero como $venta->user
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}