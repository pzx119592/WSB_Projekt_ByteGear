<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\Cart;
use App\Services\OrderMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function checkout(Request $request, Cart $cart)
    {
        if (! $cart->rows()) {
            return redirect()->route('cart')->with('status', 'Najpierw dodaj produkt do koszyka.');
        }
        $request->session()->put('checkout_token', $request->session()->get('checkout_token', (string) Str::uuid()));

        return view('checkout', [
            'rows' => $cart->rows(),
            'total' => $cart->total()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:100',
            'address' => 'required|string|min:5|max:200',
            'postcode' => ['required', 'regex:/^[0-9]{2}-[0-9]{3}$/'],
            'city' => 'required|string|min:2|max:100',
            'checkout_token' => 'required|uuid',
        ]);
        if ($data['checkout_token'] !== $request->session()->get('checkout_token')) {
            throw ValidationException::withMessages(['order' => 'Formularz zamówienia wygasł. Otwórz podsumowanie ponownie.']);
        }
        $cart = $request->session()->get('cart', []);
        if (! $cart) {
            throw ValidationException::withMessages(['order' => 'Koszyk jest pusty.']);
        }
        // Błąd podczas zakupu wycofuje zapis zamówienia i zmianę magazynu.
        $order = DB::transaction(function () use ($request, $data, $cart) {
            // Stała kolejność blokowania zapobiega zakleszczeniom między zamówieniami.
            $products = Product::whereIn('id', array_keys($cart))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $total = 0;
            foreach ($cart as $id => $quantity) {
                $product = $products->get($id);
                if (! $product || ! $product->active || ! is_int($quantity) || $quantity < 1 || $quantity > 99 || $product->stock < $quantity) {
                    throw ValidationException::withMessages(['order' => 'Zmieniła się dostępność produktu. Sprawdź koszyk.']);
                }
                $total += $product->price_grosze * $quantity;
            }
            $order = Order::create($data + [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
                'total_grosze' => $total
            ]);
            foreach ($cart as $id => $quantity) {
                $product = $products[$id];
                // Historia zachowuje nazwę i cenę z chwili zakupu.
                $order->items()->create([
                    'product_id' => $id,
                    'name' => $product->name,
                    'price_grosze' => $product->price_grosze,
                    'quantity' => $quantity
                ]);
                $product->decrement('stock', $quantity);
            }

            return $order;
        }, 3);
        $request->session()->forget(['cart', 'checkout_token']);

        $sent = app(OrderMail::class)->send($order);

        return redirect()->route('orders.show', $order)->with('status', $sent
            ? 'Zamówienie zostało złożone. Potwierdzenie wysłano e-mailem.'
            : 'Zamówienie zostało złożone. Wysyłka e-maila nie powiodła się — możesz ją ponowić w szczegółach zamówienia.');
    }

    public function cancel(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        $request->validate(['confirm_cancel' => 'required|accepted']);
        DB::transaction(function () use ($order) {
            $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($locked->status === 'cancelled') {
                return;
            }
            if ($locked->status !== 'new') {
                throw ValidationException::withMessages(['order' => 'Można anulować tylko zamówienie, którego realizacja jeszcze się nie rozpoczęła.']);
            }
            $items = $locked->items()->whereNotNull('product_id')->get()->groupBy('product_id');
            $products = Product::whereIn('id', $items->keys())->orderBy('id')->lockForUpdate()->get();
            foreach ($products as $product) {
                $product->increment('stock', $items[$product->id]->sum('quantity'));
            }
            $locked->forceFill([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ])->save();
        }, 3);

        return back()->with('status', 'Zamówienie anulowane. Produkty wróciły do magazynu.');
    }

    public function confirmation(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        if ($order->status === 'cancelled') {
            return back()->withErrors(['order' => 'Zamówienie zostało anulowane.']);
        }
        $sent = app(OrderMail::class)->send($order);

        return back()->with('status', $sent ? 'Potwierdzenie zamówienia zostało wysłane.' : 'Nie udało się wysłać potwierdzenia. Spróbuj ponownie później.');
    }

    public function index(Request $request)
    {
        return view('orders.index', ['orders' => Order::where('user_id', $request->user()->id)->latest()->paginate(15)]);
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id || in_array($request->user()->role, ['admin', 'moderator']), 403);

        return view('orders.show', ['order' => $order->load('items')]);
    }

    public function dashboard(Request $request)
    {
        return match ($request->user()->role) {
            'admin' => redirect()->route('admin.users.index'),
            'moderator' => redirect()->route('admin.products.index'),
            default => view('account', ['orders' => Order::where('user_id', $request->user()->id)->latest()->take(5)->get()]),
        };
    }
}
