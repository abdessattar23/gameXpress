<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartItemsController extends Controller
{
    public function store()
    {
        $all = CartItem::get();
        return response()->json(['message' => 'An error occurred during logout']);
    }
}
