<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{

    public function index()
    {
        return response()->json([
            'products' => Product::all(),
            'message' => 'Success'
        ], 200);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Product::create($data);

        return response()->json($product, 201);
    }


    public function show(Product $product)
    {
        return response()->json($product, 200);
    }


    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
        ]);

        $product->update($data);

        return response()->json($product, 200);
    }


    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(null, 204);
    }
}
