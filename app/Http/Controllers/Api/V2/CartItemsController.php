<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartItemsController extends Controller
{
    public function add(Request $request)
    {
        try {
            dd(Auth::id());
            $data = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'required|integer|min:1|max:' . Product::find($request->product_id)->stock
            ]);
            if (!auth()->user()) {
                $data['session_id'] = uniqid('cart_', true);
                $data['user_id'] = null;
            } else {
                $data['user_id'] = auth()->id();
                $data['session_id'] = null;
            }

            CartItem::create($data);

            return response()->json(['message' => 'Item added to cart', 'data' => $data]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error adding item to cart', 'error' => $th->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        return response()->json(['message' => 'Item updated']);
    }

    public function remove(Request $request)
    {
        return response()->json(['message' => 'Item removed']);
    }

    public function clear(Request $request)
    {
        return response()->json(['message' => 'Cart cleared']);
    }

    public function items(Request $request)
    {
        return response()->json(['message' => 'Cart items']);
    }
    public function calculateTotal($user, $sessionid)
    {
        if ($user) {
            $cartItems = CartItem::where("user_id", $user)->with('product')->get();
        } else {
            $cartItems = CartItem::where("session_id", $sessionid)->with('product')->get();
        }
        $totalPrice = 0;
        $totalQuantity = 0;

        foreach ($cartItems as $item) {
            $quantity = $item->quantity;
            $price = $item->product->price ?? 0;

            $totalPrice += $price * $quantity;
            $totalQuantity += $quantity;
        }

        return response()->json([
            'total_price' => $totalPrice,
            'total_quantity' => $totalQuantity
        ]);
    }
    public function discount(){
        
    }
    public function index()
    {

        // $t = $this->calculateTotal(null, 1);
    }
}
