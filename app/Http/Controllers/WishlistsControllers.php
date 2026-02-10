<?php

namespace App\Http\Controllers;

use App\Models\Wishlists;
use Exception;
use Illuminate\Http\Request;
use Validator;

class WishlistsControllers extends Controller
{
    public function index()
    {
        try {
            $wishlists = Wishlists::all();
            return response()->json([
                'success' => true,
                'data' => $wishlists
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

            $wishlist = Wishlists::create($request->all());
            return response()->json([
                'success' => true,
                'data' => $wishlist
            ], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $wishlist = Wishlists::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'product_id' => 'required|exists:products,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $wishlist->update($request->all());
            return response()->json([
                'success' => true,
                'data' => $wishlist
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $wishlist = Wishlists::findOrFail($id);
            $wishlist->delete();
            return response()->json([
                'success' => true,
                'message' => 'Wishlist deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $wishlist = Wishlists::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $wishlist
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getUserWishlists($userId)
    {
        try {
            $wishlists = Wishlists::where('user_id', $userId)->get();
            return response()->json([
                'success' => true,
                'data' => $wishlists
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getUserWishlistsByProduct($userId, $productId)
    {
        try {
            $wishlists = Wishlists::where('user_id', $userId)->where('product_id', $productId)->get();
            return response()->json([
                'success' => true,
                'data' => $wishlists
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
