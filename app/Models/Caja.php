<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'monto_inicial', 
        'monto_final', 
        'fecha_apertura', 
        'fecha_cierre', 
        'estado'
    ];

    // --- RELACIONES ---

    // Relación: Una caja tiene muchos gastos (ESTA FALTABA)
    public function gastos()
    {
        return $this->hasMany(Gasto::class);
    }

    // Relación: Una caja pertenece a un usuario
    public function usuario() {
        return $this->belongsTo(User::class, 'user_id');
    }

    // --- FUNCIONES DE AYUDA (HELPER) ---

    // Verifica si un usuario tiene una caja abierta actualmente
    public static function tieneCajaAbierta($userId) {
        return self::where('user_id', $userId)
                   ->whereNull('fecha_cierre')
                   ->exists();
    }
}