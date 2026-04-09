<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('status', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,_id',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'rating_avg' => 'nullable|numeric|min:0|max:5',
            'rating_count' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
            'size' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status');
        $data['rating_avg'] = $request->rating_avg ?? 0;
        $data['rating_count'] = $request->rating_count ?? 0;

        // Convert size and color strings to arrays
        if ($request->filled('size')) {
            $data['size'] = array_map('trim', explode(',', $request->size));
        }
        if ($request->filled('color')) {
            $data['color'] = array_map('trim', explode(',', $request->color));
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = Storage::url($path);
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::where('status', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,_id',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'rating_avg' => 'nullable|numeric|min:0|max:5',
            'rating_count' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
            'size' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $product = Product::findOrFail($id);
        $data = $request->all();
        $data['status'] = $request->has('status');

        // Convert size and color strings to arrays
        if ($request->filled('size')) {
            $data['size'] = array_map('trim', explode(',', $request->size));
        } else {
            $data['size'] = [];
        }

        if ($request->filled('color')) {
            $data['color'] = array_map('trim', explode(',', $request->color));
        } else {
            $data['color'] = [];
        }

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                $oldPath = str_replace('/storage/', '', $product->image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = Storage::url($path);
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        
        // Delete image
        if ($product->image) {
             $oldPath = str_replace('/storage/', '', $product->image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
        }
        
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
