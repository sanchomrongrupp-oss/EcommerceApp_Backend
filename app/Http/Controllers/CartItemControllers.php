<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Exception;
use Illuminate\Http\Request;
use Validator;

class CartItemControllers extends Controller
{
    public function index()
    {
        try {
            $cartItems = CartItem::all();
            return response()->json([
                'success' => true,
                'data' => $cartItems
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cart_id' => 'required|exists:carts,id',
                'product_id' => 'required|exists:products,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $cartItem = CartItem::create($request->all());
            return response()->json([
                'success' => true,
                'data' => $cartItem
            ], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $cartItem = CartItem::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'cart_id' => 'required|exists:carts,id',
                'product_id' => 'required|exists:products,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $cartItem->update($request->all());
            return response()->json([
                'success' => true,
                'data' => $cartItem
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $cartItem = CartItem::findOrFail($id);
            $cartItem->delete();
            return response()->json([
                'success' => true,
                'message' => 'Cart item deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $cartItem = CartItem::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $cartItem
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getCartItems($cartId)
    {
        try {
            $cartItems = CartItem::where('cart_id', $cartId)->get();
            return response()->json([
                'success' => true,
                'data' => $cartItems
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getCartItemsByProduct($cartId, $productId)
    {
        try {
            $cartItems = CartItem::where('cart_id', $cartId)->where('product_id', $productId)->get();
            return response()->json([
                'success' => true,
                'data' => $cartItems
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
