<?php

namespace App\Http\Controllers;

use App\Models\OrderItems;
use Exception;
use Illuminate\Http\Request;
use Validator;

class OrderItemsControllers extends Controller
{
    public function index()
    {
        try {
            $orderItems = OrderItems::all();
            return response()->json([
                'success' => true,
                'data' => $orderItems,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|exists:orders,id',
                'product_id' => 'required|exists:products,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $orderItem = OrderItems::create($request->all());
            return response()->json([
                'success' => true,
                'data' => $orderItem
            ], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $orderItem = OrderItems::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'order_id' => 'required|exists:orders,id',
                'product_id' => 'required|exists:products,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $orderItem->update($request->all());
            return response()->json([
                'success' => true,
                'data' => $orderItem
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $orderItem = OrderItems::findOrFail($id);
            $orderItem->delete();
            return response()->json([
                'success' => true,
                'message' => 'Order item deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $orderItem = OrderItems::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $orderItem
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getOrderItems($orderId)
    {
        try {
            $orderItems = OrderItems::where('order_id', $orderId)->get();
            return response()->json([
                'success' => true,
                'data' => $orderItems
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getOrderItemsByProduct($orderId, $productId)
    {
        try {
            $orderItems = OrderItems::where('order_id', $orderId)->where('product_id', $productId)->get();
            return response()->json([
                'success' => true,
                'data' => $orderItems
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
