<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Cart;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function index(Cart $cart)
    {
        return view('cart', ['rows' => $cart->rows(), 'total' => $cart->total()]);
    }

    public function add(Request $request, Product $product)
    {
        $data = $request->validate(['quantity' => 'required|integer|min:1|max:99']);
        $cart = $request->session()->get('cart', []);
        $quantity = ($cart[$product->id] ?? 0) + $data['quantity'];
        $this->check($product, $quantity);
        $cart[$product->id] = $quantity;
        $request->session()->put('cart', $cart);

        return back()->with('status', 'Produkt dodany do koszyka.');
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate(['quantity' => 'required|integer|min:1|max:99']);
        $this->check($product, $data['quantity']);
        $cart = $request->session()->get('cart', []);
        abort_unless(isset($cart[$product->id]), 404);
        $cart[$product->id] = (int) $data['quantity'];
        $request->session()->put('cart', $cart);

        return back()->with('status', 'Koszyk zaktualizowany.');
    }

    public function destroy(Request $request, int $product)
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$product]);
        $request->session()->put('cart', $cart);

        return back()->with('status', 'Produkt usunięty z koszyka.');
    }

    private function check(Product $product, int $quantity): void
    {
        if (! $product->active || $quantity > $product->stock || $quantity > 99) {
            throw ValidationException::withMessages(['quantity' => 'Wybrana liczba sztuk jest niedostępna.']);
        }
    }
}
