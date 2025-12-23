<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $fillable = ['user_id', 'monto_inicial', 'monto_final', 'fecha_apertura', 'fecha_cierre', 'estado'];

    public function gastos()
    {
        return $this->hasMany(Gasto::class);
    }
}