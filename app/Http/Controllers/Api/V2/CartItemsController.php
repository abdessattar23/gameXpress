<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
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

            CartItem::create($data);

            return response()->json(['message' => 'Item added to cart', 'session_id' => $data['session_id']]);
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
}
