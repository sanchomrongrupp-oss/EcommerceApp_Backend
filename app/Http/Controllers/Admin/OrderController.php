<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\OrderItems;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Orders::with('user')->latest()->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(string $id)
    {
        $order = Orders::with(['user', 'orderItems'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function edit(string $id)
    {
        $order = Orders::findOrFail($id);
        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order = Orders::findOrFail($id);
        $order->update([
            'status' => $request->status,
        ]);

        return redirect()->route('admin.orders.index')->with('success', 'Order status updated successfully.');
    }

    public function destroy(string $id)
    {
        $order = Orders::findOrFail($id);
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }
}
