<?php

namespace App\Services;

use App\Models\Product;

class Cart
{
    public function rows(): array
    {
        $cart = session('cart', []);

        return Product::with('category')->whereIn('id', array_keys($cart))->get()->map(fn ($product) => [
            'product' => $product, 'quantity' => (int) $cart[$product->id],
            'subtotal' => $product->price_grosze * (int) $cart[$product->id],
        ])->all();
    }

    public function total(): int
    {
        return array_sum(array_column($this->rows(), 'subtotal'));
    }
}
