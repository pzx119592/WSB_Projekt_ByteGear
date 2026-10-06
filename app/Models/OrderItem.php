<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['product_id', 'name', 'price_grosze', 'quantity'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
