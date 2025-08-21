<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // Get all cart items for the logged-in user
    public function index()
    {
        $items = DB::table('cart_items')
            ->where('user_id', Auth::id())
            ->get();

        $total = $items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return response()->json([
            'items' => $items,
            'total' => $total
        ]);
    }

    // Add item to cart
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer',
            'price' => 'required|numeric',
            'quantity' => 'required|integer|min:1'
        ]);

        DB::table('cart_items')->insert([
            'user_id' => Auth::id(),
            'product_id' => $data['product_id'],
            'price' => $data['price'],
            'quantity' => $data['quantity'],
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['message' => 'Item added to cart']);
    }

    // Update quantity
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        DB::table('cart_items')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->update([
                'quantity' => $data['quantity'],
                'updated_at' => now()
            ]);

        return response()->json(['message' => 'Cart updated']);
    }

    // Remove item from cart
    public function destroy($id)
    {
        DB::table('cart_items')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Item removed']);
    }
}
