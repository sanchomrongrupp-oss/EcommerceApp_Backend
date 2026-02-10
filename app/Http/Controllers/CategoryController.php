<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class CategoryController extends Controller
{
    /**
     * Display a listing of all group products.
     * GET /api/group-products
     */
    public function index()
    {
        try {
            $categories = Category::all();
            
            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully',
                'data' => $categories,
                'count' => $categories->count()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a specific group product.
     * GET /api/group-products/{id}
     */
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Category retrieved successfully',
                'data' => $category
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created category.
     * POST /api/categories
     */
    public function store(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'title'     => 'required|string|max:255',
            'status'    => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // Use $request->only to ensure only fillable data is saved
        $category = Category::create($request->only(['title', 'status']));
        $category->save();
        Log::info("Category Created: [{$category->id}] {$category->title}");

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data'    => $category
        ], 201);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

    /**
     * Update the specified category.
     * PUT/PATCH /api/categories/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            // Find the category
            $category = Category::findOrFail($id);

            // Validate the request
            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'status' => 'nullable|boolean', 
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update the category using only allowed fields
            $category->update($request->only(['title', 'status']));
            $category->save();
            Log::info("Category Updated: [{$category->id}] {$category->title}");

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /** 
     * Remove the specified category.
     * DELETE /api/categories/{id}
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Legacy method - maintained for backward compatibility
     * GET /api/addgrouppd
     */
    public function addgrouppd()
    {
        return $this->index();
    }
}
