<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $fillable = ['caja_id', 'descripcion', 'monto'];

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }
}