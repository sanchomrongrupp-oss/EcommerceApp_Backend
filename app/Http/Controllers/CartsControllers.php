<?php

namespace App\Http\Controllers;

use App\Models\Carts;
use Exception;
use Illuminate\Http\Request;
use Validator;

class CartsControllers extends Controller
{
    public function index()
    {
        try {
            $carts = Carts::all();
            return response()->json([
                'success' => true,
                'data' => $carts
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

            $cart = Carts::create($request->all());
            return response()->json([
                'success' => true,
                'data' => $cart
            ], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $cart = Carts::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'product_id' => 'required|exists:products,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $cart->update($request->all());
            return response()->json([
                'success' => true,
                'data' => $cart
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $cart = Carts::findOrFail($id);
            $cart->delete();
            return response()->json([
                'success' => true,
                'message' => 'Cart deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $cart = Carts::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $cart
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getUserCarts($userId)
    {
        try {
            $carts = Carts::where('user_id', $userId)->get();
            return response()->json([
                'success' => true,
                'data' => $carts
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getUserCartsByProduct($userId, $productId)
    {
        try {
            $carts = Carts::where('user_id', $userId)->where('product_id', $productId)->get();
            return response()->json([
                'success' => true,
                'data' => $carts
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
