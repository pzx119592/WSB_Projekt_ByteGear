<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['category_id', 'name', 'sku', 'description', 'price_grosze', 'stock', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean', 'price_grosze' => 'integer', 'stock' => 'integer'];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
