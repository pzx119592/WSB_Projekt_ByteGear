<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminProductController extends Controller
{
    public function index()
    {
        return view('admin.products.index', ['products' => Product::with('category')->latest()->paginate(20)]);
    }

    public function create()
    {
        return $this->form(new Product([
            'stock' => 0,
            'active' => true
        ]));
    }

    public function edit(Product $product)
    {
        return $this->form($product);
    }

    private function form(Product $product)
    {
        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::all()
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->data($request);
        $path = $request->hasFile('image') ? app(ProductImage::class)->store($request->file('image')) : null;
        try {
            Product::create($data + ['image_path' => $path]);
        } catch (\Throwable $exception) {
            if ($path) {
                Storage::disk('local')->delete($path);
            }
            throw $exception;
        }

        return redirect()->route('admin.products.index')->with('status', 'Produkt dodany.');
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->data($request, $product);
        $oldPath = $product->image_path;
        $newPath = $request->hasFile('image') ? app(ProductImage::class)->store($request->file('image')) : null;
        if ($newPath || $request->boolean('remove_image')) {
            $data['image_path'] = $newPath;
        }
        try {
            $product->update($data);
        } catch (\Throwable $exception) {
            if ($newPath) {
                Storage::disk('local')->delete($newPath);
            }
            throw $exception;
        }
        if ($oldPath && $oldPath !== $product->image_path) {
            Storage::disk('local')->delete($oldPath);
        }

        return redirect()->route('admin.products.index')->with('status', 'Produkt zapisany.');
    }

    public function destroy(Product $product)
    {
        // Wycofanie z oferty zachowuje produkt dla historii zamówień.
        $product->update(['active' => false]);

        return back()->with('status', 'Produkt wycofany ze sprzedaży. Możesz przywrócić go przez edycję.');
    }

    private function data(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:150',
            'category_id' => 'required|exists:categories,id',
            'sku' => [
                'required',
                'string',
                'max:40',
                'regex:/^[A-Z0-9-]+$/',
                Rule::unique('products')->ignore($product)
            ],
            'description' => 'required|string|min:10|max:5000',
            'price' => 'required|numeric|decimal:0,2|gt:0|max:1000000',
            'stock' => 'required|integer|min:0|max:10000',
            'active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096|dimensions:min_width=160,min_height=160,max_width=4096,max_height=4096|prohibited_if:remove_image,1',
            'remove_image' => 'nullable|boolean',
        ]);
        $data['price_grosze'] = (int) round($data['price'] * 100);
        unset($data['price']);
        unset($data['image'], $data['remove_image']);
        $data['active'] = $request->boolean('active');

        return $data;
    }
}
