<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(){
        $products = Product::count();
        $users = User::count();
        $categories = Category::count();
        return response()->json([
            "message" => 'This is The admin board',
            'products' => $products,
            'users'=> $users,
            'categories'=> $categories,
        ]);
    }

}
