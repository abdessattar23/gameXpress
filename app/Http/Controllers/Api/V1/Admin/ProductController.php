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

        if(!auth()->user()->can('view_products')){
            return response()->json([
                'message' => 'You do not have permission to view products'
            ], 403);
        }

        $products = Product::all();

        return response()->json([
            'products_list' => $products,
            'message' => 'Success'
        ], 200);
    }


    public function store(Request $request)
    {

        if(!auth()->user()->can('create_products')){
            return response()->json([
                'message' => 'You do not have permission to create products'
            ], 403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:products',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Product::create($data);

        return response()->json($product, 201);
    }


    public function show($id)
    {

        if (!auth()->user()->can('view_products')){
            return response()->json([
                'message' => 'You do not have permission to view this product'
            ], 403);
        }

        $product = Product::find($id);

        if(!$product){
            return response()->json([
                'message' => 'selected product does not exist',
                'status' => 'error 404'
            ], 404);
        }

        return response()->json($product, 200);
    }


    public function update(Request $request, $id)
    {

        if(!auth()->user()->can('edit_products')){
            return response()->json([
                'message' => 'You do not have permission to edit this product'
            ], 403);
        }

        $product = Product::find($id);

        if(!$product){
            return response()->json([
                'message' => 'Selected product does not exist',
                'status' => 'error 404'
            ], 404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:products',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product->update($data);

        return response()->json([
            'updated_product' => $product
        ],200);
    }


    public function destroy($id)
    {
        if(!auth()->user()->can('delete_products')){
            return response()->json([
                'message' => 'You do not have permission to delete this product'
            ], 403);
        }
        $product = Product::find($id);
        if(!$product){
            return response()->json([
                'message' => 'Selected product does not exist',
                'status' => 'error 404'
            ], 404);
        }
        $product->delete();
        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }

    public function restore($id)
    {

        $product = Product::withTrashed()->find($id);

        if(!$product){
            return response()->json([
                'message' => 'Selected product does not exist',
                'status' => 'error 404'
            ], 404);
        }

        if(!$product->trashed()){
            return response()->json([
                'message' => 'Product is not deleted',
                'status' => 'error 400'
            ], 400);
        }

        $product->restore();

        return response()->json([
            'message' => 'Product restored successfully',
            'product' => $product
        ], 200);
    }
}
