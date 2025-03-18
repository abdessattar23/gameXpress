<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use Laravel\Sanctum\PersonalAccessToken;

class CartItemsController extends Controller
{
    public function add(Request $request)
    {
        try {
            $data = [
                'session_id' => null,
                'user_id' => null
            ];
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'required|integer|min:1|max:' . Product::find($request->product_id)->stock,
                'session_id' => 'nullable',
            ]);
            $data = array_merge($data, $validated);

            if (!AuthController::validToken($request->bearerToken())) {
                if (!$data['session_id']) {
                    $data['session_id'] = uniqid('cart_', true);
                }
            } else {
                $data['user_id'] = PersonalAccessToken::findToken($request->bearerToken())->tokenable->id;
                $data['session_id'] = null;
            }

            $existingItem = CartItem::where([
                'session_id' => $data['session_id'],
                'product_id' => $data['product_id']
            ])->first();

            if ($existingItem) {
                $newQuantity = $existingItem->quantity + $data['quantity'];
                $product = Product::find($data['product_id']);

                if ($newQuantity > $product->stock) {
                    return response()->json(['message' => 'Requested quantity exceeds available stock'], 422);
                }

                $existingItem->update(['quantity' => $newQuantity]);
            } else {
                CartItem::create($data);
            }

            return response()->json(['message' => 'Item added to cart', 'session_id' => $data['session_id']]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error adding item to cart', 'error' => $th->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        return response()->json(['message' => 'Item updated']);
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
                CartItem::whereNull('user_id')->where('session_id', $sessionId)->delete();
            } else {
                $sessionItem->user_id = $userId;
                $sessionItem->session_id = null;
                $sessionItem->save();
            }
        }
        return response()->json(['message' => 'Cart items merged']);
    }

    public function removeFromCart($id)
    {
        $row = CartItem::findOrFail($id)->delete();
        dd($row);

        $this->calculateTotal($row->user_id, $row->session_id);

        return response()->json(['message' => 'Item removed from cart']);
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


}
