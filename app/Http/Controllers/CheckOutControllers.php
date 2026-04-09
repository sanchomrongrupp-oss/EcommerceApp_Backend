<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\CheckoutItems;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class CheckOutControllers extends Controller
{
    public function process(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method_id' => 'required' // From Stripe Elements (Frontend)
        ]);

        $user = $request->user();
        $totalAmount = 0;

        // Use a Transaction to ensure database integrity
        return DB::transaction(function () use ($request, $user, &$totalAmount) {
            
            // 1. Create the Main Order
            $order = Orders::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'total_amount' => 0, // Will update shortly
            ]);

            // 2. Loop through items, calculate price, and create OrderItems
            foreach ($request->items as $item) {
                $product = Product::find($item['id']);
                $itemTotal = $product->price * $item['quantity'];
                $totalAmount += $itemTotal;

                CheckoutItems::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total' => $itemTotal,
                ]);
            }

            // 3. Update Order with final total
            $order->update(['total_amount' => $totalAmount]);

            // 4. Process Payment with Stripe (Laravel Cashier)
            try {
                // Stripe expects amounts in CENTS
                $payment = $user->charge($totalAmount * 100, $request->payment_method_id, [
                    'return_url' => route('checkout.success'),
                ]);

                $order->update(['status' => 'paid', 'stripe_id' => $payment->id]);

                return response()->json([
                    'message' => 'Payment successful',
                    'order_id' => $order->id
                ], 200);

            } catch (\Exception $e) {
                $order->update(['status' => 'failed']);
                return response()->json(['error' => $e->getMessage()], 500);
            }
        });
    }
}