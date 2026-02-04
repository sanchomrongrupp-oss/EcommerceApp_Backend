<?php

namespace App\Http\Controllers;

use App\Models\GroupProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class GroupProductController extends Controller
{
    /**
     * Display a listing of all group products.
     * GET /api/group-products
     */
    public function index()
    {
        try {
            $group_products = GroupProduct::all();
            
            return response()->json([
                'success' => true,
                'message' => 'Group products retrieved successfully',
                'data' => $group_products,
                'count' => $group_products->count()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve group products',
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
            $group_product = GroupProduct::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Group product retrieved successfully',
                'data' => $group_product
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Group product not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve group product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created group product.
     * POST /api/group-products
     */
    public function store(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'title'     => 'required|string|max:255',
            'status'    => 'nullable|boolean',
            'parent_id' => 'nullable', // Allow parent_id to be passed
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // Use $request->only to ensure only fillable data is saved
        $group_product = GroupProduct::create($request->only(['title', 'status', 'parent_id']));

        return response()->json([
            'success' => true,
            'message' => 'Group product created successfully',
            'data'    => $group_product
        ], 201);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

    /**
     * Update the specified group product.
     * PUT/PATCH /api/group-products/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            // Find the group product
            $group_product = GroupProduct::findOrFail($id);

            // Validate the request
            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'status' => 'nullable|boolean',
                'parent_id' => 'nullable', 
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update the group product using only allowed fields
            $group_product->update($request->only(['title', 'status', 'parent_id']));

            return response()->json([
                'success' => true,
                'message' => 'Group product updated successfully',
                'data' => $group_product
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Group product not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update group product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified group product.
     * DELETE /api/group-products/{id}
     */
    public function destroy($id)
    {
        try {
            $group_product = GroupProduct::findOrFail($id);
            $group_product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Group product deleted successfully'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Group product not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete group product',
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
