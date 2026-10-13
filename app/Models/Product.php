<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['category_id', 'name', 'sku', 'description', 'price_grosze', 'stock', 'active'];

    public function imagePath(): string
    {
        $key = strtolower($this->sku);
        if (preg_match('/^(keyboard|mouse|headset|monitor|pad|usb)-[1-3]$/', $key)) {
            $file = str_ends_with($key, '-1') ? substr($key, 0, -2) : $key;

            return 'images/products/'.$file.'.png';
        }

        // Nowy produkt dodany w panelu otrzymuje domyślną wizualizację swojej kategorii.
        return 'images/products/'.$this->category->slug.'.png';
    }

    protected function casts(): array
    {
        return ['active' => 'boolean', 'price_grosze' => 'integer', 'stock' => 'integer'];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
