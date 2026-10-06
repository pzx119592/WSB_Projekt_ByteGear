<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminProductController extends Controller
{
    public function index()
    {
        return view('admin.products.index', ['products' => Product::with('category')->latest()->paginate(20)]);
    }

    public function create()
    {
        return $this->form(new Product(['stock' => 0, 'active' => true]));
    }

    public function edit(Product $product)
    {
        return $this->form($product);
    }

    private function form(Product $product)
    {
        return view('admin.products.form', ['product' => $product, 'categories' => Category::all()]);
    }

    public function store(Request $request)
    {
        Product::create($this->data($request));

        return redirect()->route('admin.products.index')->with('status', 'Produkt dodany.');
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->data($request, $product));

        return redirect()->route('admin.products.index')->with('status', 'Produkt zapisany.');
    }

    public function destroy(Product $product)
    {
        $product->update(['active' => false]);

        return back()->with('status', 'Produkt wycofany ze sprzedaży. Możesz przywrócić go przez edycję.');
    }

    private function data(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:150', 'category_id' => 'required|exists:categories,id',
            'sku' => ['required', 'string', 'max:40', 'regex:/^[A-Z0-9-]+$/', Rule::unique('products')->ignore($product)],
            'description' => 'required|string|min:10|max:5000', 'price' => 'required|numeric|decimal:0,2|gt:0|max:1000000',
            'stock' => 'required|integer|min:0|max:10000', 'active' => 'nullable|boolean',
        ]);
        $data['price_grosze'] = (int) round($data['price'] * 100);
        unset($data['price']);
        $data['active'] = $request->boolean('active');

        return $data;
    }
}
