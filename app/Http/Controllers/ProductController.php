<?php


namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{

    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'catagory' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $request->name,
            'catagory' => $request->catagory,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image_path' => $imagePath,
            'created_by' => Auth::id(), // Use Auth facade to retrieve authenticated user
            'updated_by' => Auth::id(),
        ]);

        return response()->json($product, 201);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'catagory' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = $product->image_path;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name' => $request->name,
            'catagory' => $request->catagory,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image_path' => $imagePath,
            'updated_by' => Auth::id(),
        ]);

        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
