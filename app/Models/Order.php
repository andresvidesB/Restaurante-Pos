<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total',
        'delivery_cost',
        'total_with_delivery',
        'payment_method',
        'payment_proof',
        'status',
        'notes',
    ];

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}