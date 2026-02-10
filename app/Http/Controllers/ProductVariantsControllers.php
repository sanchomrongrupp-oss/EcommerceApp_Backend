<?php

namespace App\Http\Controllers;

use App\Models\ProductVariants;
use Exception;
use Illuminate\Http\Request;
use Validator;

class ProductVariantsControllers extends Controller
{
    public function index()
    {
        try {
            $productVariants = ProductVariants::all();
            return response()->json([
                'success' => true,
                'data' => $productVariants
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'size' => 'required|string|max:255',
                'color' => 'required|string|max:255',
                'price' => 'required|numeric',
                'stock' => 'required|integer',
                'sku' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $productVariant = ProductVariants::create($request->all());
            return response()->json([
                'success' => true,
                'data' => $productVariant
            ], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $productVariant = ProductVariants::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'size' => 'required|string|max:255',
                'color' => 'required|string|max:255',
                'price' => 'required|numeric',
                'stock' => 'required|integer',
                'sku' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $productVariant->update($request->all());
            return response()->json([
                'success' => true,
                'data' => $productVariant
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $productVariant = ProductVariants::findOrFail($id);
            $productVariant->delete();
            return response()->json([
                'success' => true,
                'message' => 'Product variant deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $productVariant = ProductVariants::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $productVariant
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getProductVariants($productId)
    {
        try {
            $productVariants = ProductVariants::where('product_id', $productId)->get();
            return response()->json([
                'success' => true,
                'data' => $productVariants
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getProductVariantsByColor($productId, $color)
    {
        try {
            $productVariants = ProductVariants::where('product_id', $productId)->where('color', $color)->get();
            return response()->json([
                'success' => true,
                'data' => $productVariants
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getProductVariantsBySize($productId, $size)
    {
        try {
            $productVariants = ProductVariants::where('product_id', $productId)->where('size', $size)->get();
            return response()->json([
                'success' => true,
                'data' => $productVariants
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getProductVariantsByColorAndSize($productId, $color, $size)
    {
        try {
            $productVariants = ProductVariants::where('product_id', $productId)->where('color', $color)->where('size', $size)->get();
            return response()->json([
                'success' => true,
                'data' => $productVariants
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
