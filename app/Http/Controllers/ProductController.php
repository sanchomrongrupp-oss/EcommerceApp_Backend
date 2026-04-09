<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        try {
            $query = Product::query();

            // Optional filtering by group
            if ($request->has('group_product_id')) {
                $query->where('group_product_id', $request->group_product_id);
            }

            // Optional filtering by category title
            if ($request->has('category')) {
                $categoryTitle = $request->category;
                $query->whereHas('category', function ($q) use ($categoryTitle) {
                    $q->where('title', 'like', '%' . $categoryTitle . '%');
                });
            }

            $perPage = $request->get('per_page', 10);
            $products = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'next_page_url' => $products->nextPageUrl(),
                    'prev_page_url' => $products->previousPageUrl(),
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display a specific product.
     */
    public function show($id)
    {
        try {
            $product = Product::with('groups')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $product
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a new product.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category_id' => 'required|exists:categories,_id',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'required', // Can be file or string (URL)
                    'price' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $data = $request->all();

            // Handle file upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                $data['image'] = Storage::url($path);
            }

            $product = Product::create($data);

            Log::info("Product Created: [{$product->id}] {$product->title}");

            return response()->json(['success' => true, 'data' => $product], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update a product.
     */
    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'category_id' => 'required|exists:categories,_id',
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'image'=> 'sometimes|required',
                'price' => 'sometimes|required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $data = $request->all();

            if ($request->hasFile('image')) {
                if ($product->image && str_contains($product->image, 'public/storage/')) {
                    $oldPath = str_replace('public/storage/', '', $product->image);
                    Storage::disk('public')->delete($oldPath);
                }

                $path = $request->file('image')->store('products', 'public');
                $data['image'] = Storage::url($path);
            }

            $product->update($data);

            Log::info("Product Updated: [{$product->id}] {$product->title}");

            return response()->json(['success' => true, 'data' => $product], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove a product.
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return response()->json(['success' => true, 'message' => 'Product deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get the top 5 selling products.
     */
    public function topSales()
    {
        try {
            // Since we use MongoDB and don't have a direct 'sales_count' on Product,
            // we can aggregate from OrderItems or use rating_count as a popularity proxy.
            // For now, we'll use rating_count and price as weight, or simply get top 5 products.
            
            $products = Product::where('status', true)
                ->orderBy('rating_count', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Top sale products retrieved successfully',
                'data' => $products
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
