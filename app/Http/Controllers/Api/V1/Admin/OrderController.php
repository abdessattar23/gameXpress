<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy("created_at", "desc")->paginate(10);
        return response()->json([
            "message" => "success",
            "orders" => $orders
        ]);
    }
}
