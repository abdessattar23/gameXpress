<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartItemsController extends Controller
{
    public function add(Request $request)
    {
        try {

            $data = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'required|integer|min:1|max:' . Product::find($request->product_id)->stock
            ]);
            if(!auth()->user()){
                $data['session_id'] = uniqid('cart_', true);
                $data['user_id'] = null;
            }else{
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

    public function removeFromCart($id)
    {


        $row = CartItem::findOrFail($id)->delete();


        $total = CartItem::where('user_id', auth()->id())
                        ->join('products', 'card_items.product_id', '=', 'products.id')
                        ->sum(DB::raw('card_items.quantity * products.price'));

        return response()->json(['message' => 'Item removed from cart', 'new_total' => $total]);
    }

    public function clear(Request $request)
    {
        return response()->json(['message' => 'Cart cleared']);
    }

    public function items(Request $request)
    {
        return response()->json(['message' => 'Cart items']);
    }


    public static function mergeCartItems($sessionId, $userId){

        $sessionCartItems = CartItem::whereNull('user_id')
            ->where('session_id', $sessionId)
            ->get();
        // dd($sessionCartItems);

        foreach ($sessionCartItems as $sessionItem) {

            $existingItem = CartItem::where('user_id', $userId)
                ->where('product_id', $sessionItem->product_id)
                ->first();
            // dd($existingItem);

            if ($existingItem) {
                $existingItem->quantity += $sessionItem->quantity;
                $existingItem->save();
            } else {
                $sessionItem->user_id = $userId;
                $sessionItem->session_id = null;
                $sessionItem->save();
            }
        }
        CartItem::whereNull('user_id')->where('session_id', $sessionId)->delete();
        return response()->json(['message' => 'Cart items merged']);
    }
}
