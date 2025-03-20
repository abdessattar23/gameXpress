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
    public function delete($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                "message" => "Order not found"
            ], 404);
        }

        if ($order->status != "expédiée" && $order->status != "annulée") {
            $order->update(["status" => "annulée"]);
            return response()->json([
                "message" => "The process has completed successfully"
            ]);
        } elseif ($order->status == "expédiée") {
            return response()->json([
                "message" => "The order is already expédiée"
            ]);
        } else {
            return response()->json([
                "message" => "The order is already canceled"
            ]);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                "message" => "Order not found"
            ], 404);
        }

        $validStatuses = ['en attente', 'en cours', 'expédiée', 'annulée'];

        if (!in_array($request->status, $validStatuses)) {
            return response()->json([
                "message" => "Invalid status"
            ], 400);
        }

        $order->update(['status' => $request->status]);

        return response()->json([
            "message" => "Status updated successfully",
            "order" => $order
        ]);
    }

    public function show($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                "message" => "Order not found"
            ], 404);
        }

        return response()->json([
            "message" => "success",
            "order" => $order
        ]);
    }
}
