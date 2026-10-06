<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const STATUSES = ['new' => 'Nowe', 'processing' => 'W realizacji', 'completed' => 'Zrealizowane'];

    protected $fillable = ['user_id', 'checkout_token', 'name', 'email', 'address', 'postcode', 'city', 'status', 'total_grosze'];

    protected function casts(): array
    {
        return ['total_grosze' => 'integer'];
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
