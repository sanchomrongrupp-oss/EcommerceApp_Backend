<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use Exception;
use Illuminate\Http\Request;
use Validator;

class OrdersControllers extends Controller
{
    public function index()
    {
        try {
            $orders = Orders::all();
            return response()->json([
                'success' => true,
                'data' => $orders
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'product_id' => 'required|exists:products,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $order = Orders::create($request->all());
            return response()->json([
                'success' => true,
                'data' => $order
            ], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $order = Orders::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'product_id' => 'required|exists:products,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $order->update($request->all());
            return response()->json([
                'success' => true,
                'data' => $order
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $order = Orders::findOrFail($id);
            $order->delete();
            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $order = Orders::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $order
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getUserOrders($userId)
    {
        try {
            $orders = Orders::where('user_id', $userId)->get();
            return response()->json([
                'success' => true,
                'data' => $orders
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getUserOrdersByProduct($userId, $productId)
    {
        try {
            $orders = Orders::where('user_id', $userId)->where('product_id', $productId)->get();
            return response()->json([
                'success' => true,
                'data' => $orders
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
