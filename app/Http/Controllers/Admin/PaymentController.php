<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of ABA transactions.
     */
    public function index()
    {
        // Filter orders that have an ABA transaction ID
        $payments = Orders::with('user')
            ->whereNotNull('aba_tran_id')
            ->latest()
            ->get();

        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Display the specified transaction detail.
     */
    public function show(string $id)
    {
        $order = Orders::with(['user', 'orderItems'])->findOrFail($id);
        return view('admin.payments.show', compact('order'));
    }
}
