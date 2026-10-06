<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminOrderController extends Controller
{
    public function index()
    {
        return view('orders.index', ['orders' => Order::latest()->paginate(20), 'staff' => true]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate(['status' => 'required|in:processing,completed']);
        DB::transaction(function () use ($order, $data) {
            $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $allowed = ['new' => 'processing', 'processing' => 'completed'];
            if (($allowed[$locked->status] ?? null) !== $data['status']) {
                throw ValidationException::withMessages(['status' => 'Zamówienie można przesunąć tylko do następnego etapu.']);
            }
            $locked->update($data);
        });

        return back()->with('status', 'Status zamówienia zaktualizowany.');
    }
}
