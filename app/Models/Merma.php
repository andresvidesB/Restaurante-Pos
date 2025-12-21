<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merma extends Model
{
    use HasFactory;

    protected $fillable = ['insumo_id', 'cantidad', 'motivo', 'fecha', 'user_id'];

    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}