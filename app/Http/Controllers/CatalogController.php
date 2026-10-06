<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => 'nullable|string|max:100', 'category' => 'nullable|integer|exists:categories,id',
            'sort' => 'nullable|in:new,price_asc,price_desc,name', 'available' => 'nullable|in:1',
            'min' => 'nullable|numeric|min:0|max:1000000', 'max' => 'nullable|numeric|min:0|max:1000000',
        ]);
        $query = Product::with('category')->where('active', true);
        if (! empty($filters['q'])) {
            $query->where('name', 'like', '%'.$filters['q'].'%');
        }
        if (! empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }
        if (! empty($filters['available'])) {
            $query->where('stock', '>', 0);
        }
        if (isset($filters['min'])) {
            $query->where('price_grosze', '>=', round($filters['min'] * 100));
        }
        if (isset($filters['max'])) {
            $query->where('price_grosze', '<=', round($filters['max'] * 100));
        }
        [$column, $direction] = match ($filters['sort'] ?? 'new') {
            'price_asc' => ['price_grosze', 'asc'], 'price_desc' => ['price_grosze', 'desc'],
            'name' => ['name', 'asc'], default => ['id', 'desc'],
        };

        return view('catalog.index', ['products' => $query->orderBy($column, $direction)->paginate(12)->withQueryString(),
            'categories' => Category::orderBy('id')->get()]);
    }

    public function show(Product $product)
    {
        abort_unless($product->active, 404);

        return view('catalog.show', compact('product'));
    }

    public function euro(Product $product)
    {
        abort_unless($product->active, 404);
        try {
            $rate = Http::acceptJson()->connectTimeout(3)->timeout(8)
                ->get('https://api.nbp.pl/api/exchangerates/rates/A/EUR/')->throw()->json();
            $mid = $rate['rates'][0]['mid'] ?? null;
            if (! is_numeric($mid) || $mid <= 0) {
                throw new \RuntimeException('Brak kursu EUR');
            }

            return view('catalog.euro', ['product' => $product, 'rate' => $rate, 'euro' => $product->price_grosze / 100 / $mid]);
        } catch (ConnectionException|\Illuminate\Http\Client\RequestException|\RuntimeException $exception) {
            report($exception);

            return view('catalog.euro', ['product' => $product, 'error' => 'Kurs EUR jest chwilowo niedostępny. Cena i zamówienia w PLN działają normalnie.']);
        }
    }
}
